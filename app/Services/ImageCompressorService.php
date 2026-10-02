<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Laravel\Facades\Image;

class ImageCompressorService
{
    private const MAX_FILE_SIZE = 2 * 1024 * 1024;

    private const MAX_WIDTH = 1920;

    private const MAX_HEIGHT = 1920;

    private const MIN_QUALITY = 20;

    public function compress(UploadedFile $file): UploadedFile
    {
        $image = Image::decode($file->getRealPath());

        $width = $image->width();
        $height = $image->height();

        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            $image->scaleDown(self::MAX_WIDTH, self::MAX_HEIGHT);
        }

        $tempPath = sys_get_temp_dir().'/'.uniqid('img_', true).'.jpg';

        $quality = 85;
        do {
            $image->toJpeg($quality)->save($tempPath);
            $size = filesize($tempPath);
            if ($size <= self::MAX_FILE_SIZE) {
                break;
            }
            $quality -= 10;
        } while ($quality >= self::MIN_QUALITY);

        // ponytail: clean up temp file after request completes to prevent disk clutter
        register_shutdown_function(static function () use ($tempPath): void {
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        });

        return new UploadedFile(
            $tempPath,
            pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'.jpg',
            'image/jpeg',
            null,
            true
        );
    }
}
