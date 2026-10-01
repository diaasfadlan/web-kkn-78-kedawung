<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaStorageService
{
    public function __construct(
        private readonly Client $http = new Client([
            'timeout' => 60,
            'connect_timeout' => 15,
        ])
    ) {
    }

    public function uploadPublicFile(UploadedFile $file, string $directory, string $prefix): array
    {
        $extension = $file->extension() ?: $file->guessExtension() ?: 'bin';
        $filename = uniqid($prefix.'_', true).'.'.$extension;
        $publicId = trim($directory, '/').'/'.pathinfo($filename, PATHINFO_FILENAME);

        if (! $this->isCloudinaryConfigured()) {
            $storedPath = Storage::disk('public')->putFileAs(trim($directory, '/'), $file, $filename);

            return [
                'path' => $storedPath,
                'public_id' => null,
                'url' => asset('storage/'.$storedPath),
            ];
        }

        $response = $this->http->post($this->uploadEndpoint(), [
            'auth' => [
                $this->apiKey(),
                $this->apiSecret(),
            ],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $filename,
                    'headers' => [
                        'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
                    ],
                ],
                [
                    'name' => 'public_id',
                    'contents' => $publicId,
                ],
                [
                    'name' => 'overwrite',
                    'contents' => 'false',
                ],
                [
                    'name' => 'resource_type',
                    'contents' => 'image',
                ],
            ],
        ]);

        $payload = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return [
            'path' => $payload['public_id'],
            'public_id' => $payload['public_id'],
            'url' => $payload['secure_url'],
        ];
    }

    public function isCloudinaryConfigured(): bool
    {
        return ! empty($this->cloudName()) && ! empty($this->apiKey()) && ! empty($this->apiSecret());
    }

    public function deleteUploadedAsset(?string $publicId, ?string $url = null): void
    {
        if ($publicId && $this->isCloudinaryConfigured()) {
            $timestamp = time();
            $signature = sha1("public_id={$publicId}&timestamp={$timestamp}{$this->apiSecret()}");

            try {
                $this->http->post($this->destroyEndpoint(), [
                    'auth' => [
                        $this->apiKey(),
                        $this->apiSecret(),
                    ],
                    'form_params' => [
                        'public_id' => $publicId,
                        'timestamp' => $timestamp,
                        'signature' => $signature,
                        'api_key' => $this->apiKey(),
                        'resource_type' => 'image',
                        'invalidate' => true,
                    ],
                ]);
            } catch (\Throwable) {
                // Ignore remote deletion errors
            }

            return;
        }

        $this->deleteByUrl($url);
    }

    public function deleteByUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));
        $storagePosition = strpos($path, '/storage/');
        if ($storagePosition !== false) {
            Storage::disk('public')->delete(substr($path, $storagePosition + strlen('/storage/')));

            return;
        }

        if (str_contains((string) parse_url($url, PHP_URL_HOST), 'res.cloudinary.com')) {
            $publicId = $this->extractCloudinaryPublicId($path);
            if ($publicId) {
                $this->deleteUploadedAsset($publicId);
            }
        }
    }

    private function uploadEndpoint(): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/image/upload',
            $this->cloudName()
        );
    }

    private function destroyEndpoint(): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/image/destroy',
            $this->cloudName()
        );
    }

    private function cloudName(): string
    {
        return (string) config('services.cloudinary.cloud_name');
    }

    private function apiKey(): string
    {
        return (string) config('services.cloudinary.api_key');
    }

    private function apiSecret(): string
    {
        return (string) config('services.cloudinary.api_secret');
    }

    private function extractCloudinaryPublicId(string $path): ?string
    {
        $parts = array_values(array_filter(explode('/', trim($path, '/'))));
        $uploadIndex = array_search('upload', $parts, true);

        if ($uploadIndex === false) {
            return null;
        }

        $publicIdParts = array_slice($parts, $uploadIndex + 1);
        if ($publicIdParts === []) {
            return null;
        }

        if (preg_match('/^v\d+$/', $publicIdParts[0])) {
            array_shift($publicIdParts);
        }

        if ($publicIdParts === []) {
            return null;
        }

        $last = array_pop($publicIdParts);
        $publicIdParts[] = pathinfo($last, PATHINFO_FILENAME);

        return implode('/', $publicIdParts);
    }
}
