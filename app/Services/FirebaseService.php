<?php

namespace App\Services;

use GuzzleHttp\Exception\ConnectException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class FirebaseService
{
    private const COLLECTION_LIMITS = [null, 3, 6, 8];

    protected mixed $firestore = null;

    protected mixed $storage = null;

    protected mixed $auth = null;

    private bool $readCircuitOpen = false;

    /**
     * Get Firestore database instance
     */
    public function firestore(): mixed
    {
        return $this->firestore ??= app('firebase.firestore')->database();
    }

    /**
     * Get Storage instance
     */
    public function storage(): mixed
    {
        return $this->storage ??= app('firebase.storage');
    }

    /**
     * Get Auth instance
     */
    public function auth(): mixed
    {
        return $this->auth ??= app('firebase.auth');
    }

    /**
     * Get a single document from Firestore
     */
    public function getDocument(string $collection, string $documentId): ?array
    {
        $cache = Cache::store('file');
        $cacheKey = $this->documentCacheKey($collection, $documentId);

        if ($cache->has($cacheKey)) {
            $data = $cache->get($cacheKey);
            $cache->forever($this->staleCacheKey($cacheKey), $data);

            return $data;
        }

        // Collection pages are commonly opened before edit/detail pages. Reuse
        // that snapshot so an unavailable Firestore endpoint does not turn an
        // existing document into a false 404.
        $cachedDocument = $this->findDocumentInCachedCollection($collection, $documentId, false);
        if ($cachedDocument !== null) {
            $this->cacheDocument($collection, $documentId, $cachedDocument);

            return $cachedDocument;
        }

        if ($this->isReadCircuitOpen()) {
            return $this->cachedFallback($cacheKey, null)
                ?? $this->findDocumentInCachedCollection($collection, $documentId, true);
        }

        try {
            if ($this->hasPendingWrites()) {
                $this->flushPendingWrites();
            }

            return $cache->remember(
                $cacheKey,
                config('firebase.cache_ttl', 300),
                function () use ($cache, $cacheKey, $collection, $documentId): ?array {
                    $document = $this->firestore()
                        ->collection($collection)
                        ->document($documentId)
                        ->snapshot($this->readOptions());

                    if (! $document->exists()) {
                        return null;
                    }

                    $data = $document->data();
                    $data['id'] = $document->id();
                    $cache->forever($this->staleCacheKey($cacheKey), $data);
                    $cache->forget($this->circuitCacheKey());

                    return $data;
                }
            );
        } catch (Throwable $e) {
            $this->openReadCircuit($e, $collection);

            return $this->cachedFallback($cacheKey, null)
                ?? $this->findDocumentInCachedCollection($collection, $documentId, true);
        }
    }

    /**
     * Get all documents from a collection
     */
    public function getCollection(string $collection, ?int $limit = null): array
    {
        $cache = Cache::store('file');
        $cacheKey = $this->collectionCacheKey($collection, $limit);

        if ($cache->has($cacheKey)) {
            $data = $cache->get($cacheKey);
            $cache->forever($this->staleCacheKey($cacheKey), $data);

            return $data;
        }

        // A full collection snapshot can safely serve every limited homepage
        // variant without another network request.
        $cachedCollection = $this->cachedCollection($collection, false);
        if ($cachedCollection !== null) {
            $data = $limit === null
                ? $cachedCollection
                : array_slice($cachedCollection, 0, $limit);
            $this->cacheCollectionVariant($collection, $limit, $data);

            return $data;
        }

        if ($this->isReadCircuitOpen()) {
            $fallback = $this->cachedFallback($cacheKey, null)
                ?? $this->cachedCollection($collection, true)
                ?? [];

            return $limit === null ? $fallback : array_slice($fallback, 0, $limit);
        }

        try {
            if ($this->hasPendingWrites()) {
                $this->flushPendingWrites();
            }

            return $cache->remember(
                $cacheKey,
                config('firebase.cache_ttl', 300),
                function () use ($cache, $collection, $limit): array {
                    $query = $this->firestore()->collection($collection);
                    $documents = $query->documents($this->readOptions([
                        'maxRetries' => 0,
                    ]));

                    $data = [];
                    foreach ($documents as $doc) {
                        if ($doc->exists()) {
                            $docData = $doc->data();
                            $docData['id'] = $doc->id();
                            $data[] = $docData;
                        }
                    }
                    $this->cacheCollection($collection, $data);
                    $cache->forget($this->circuitCacheKey());

                    return $limit === null ? $data : array_slice($data, 0, $limit);
                }
            );
        } catch (Throwable $e) {
            $this->openReadCircuit($e, $collection);

            return $this->cachedFallback($cacheKey, []);
        }
    }

    /**
     * Add a document to a collection (auto-generated ID)
     */
    public function addDocument(string $collection, array $data): string
    {
        $docRef = $this->firestore()
            ->collection($collection)
            ->newDocument();
        $documentId = $docRef->id();

        $this->performWrite(
            fn () => $docRef->set($data, $this->writeOptions()),
            [
                'operation' => 'set',
                'collection' => $collection,
                'document_id' => $documentId,
                'data' => $data,
                'merge' => false,
            ]
        );
        $this->cacheAddedDocument($collection, $documentId, $data);

        return $documentId;
    }

    /**
     * Set (create or overwrite) a document
     */
    public function setDocument(string $collection, string $documentId, array $data, bool $merge = false): void
    {
        $document = $this->firestore()
            ->collection($collection)
            ->document($documentId);
        $this->performWrite(
            fn () => $document->set($data, $this->writeOptions(['merge' => $merge])),
            [
                'operation' => 'set',
                'collection' => $collection,
                'document_id' => $documentId,
                'data' => $data,
                'merge' => $merge,
            ]
        );
        $this->cacheChangedDocument($collection, $documentId, $data, $merge);
    }

    /**
     * Update specific fields in a document
     */
    public function updateDocument(string $collection, string $documentId, array $data): void
    {
        $updates = [];
        foreach ($data as $field => $value) {
            $updates[] = ['path' => $field, 'value' => $value];
        }
        $document = $this->firestore()
            ->collection($collection)
            ->document($documentId);
        $this->performWrite(
            fn () => $document->update($updates, $this->writeOptions()),
            [
                'operation' => 'set',
                'collection' => $collection,
                'document_id' => $documentId,
                'data' => $data,
                'merge' => true,
            ]
        );
        $this->cacheChangedDocument($collection, $documentId, $data, true);
    }

    /**
     * Delete a document
     */
    public function deleteDocument(string $collection, string $documentId): void
    {
        $document = $this->firestore()
            ->collection($collection)
            ->document($documentId);
        $this->performWrite(
            fn () => $document->delete($this->writeOptions()),
            [
                'operation' => 'delete',
                'collection' => $collection,
                'document_id' => $documentId,
            ]
        );
        $this->cacheDeletedDocument($collection, $documentId);
    }

    public function forgetCollection(string $collection): void
    {
        $cache = Cache::store('file');
        foreach (self::COLLECTION_LIMITS as $limit) {
            $key = $this->collectionCacheKey($collection, $limit);
            $cache->forget($key);
            $cache->forget($this->staleCacheKey($key));
        }

        // A successful write proves Firebase is reachable again. Do not let
        // an earlier read failure hide the newly written data on redirect.
        $this->readCircuitOpen = false;
        $cache->forget($this->circuitCacheKey());
    }

    public function forget(string $collection, string $documentId): void
    {
        $cache = Cache::store('file');
        $key = $this->documentCacheKey($collection, $documentId);
        $cache->forget($key);
        $cache->forget($this->staleCacheKey($key));
        $this->forgetCollection($collection);
    }

    private function readOptions(array $overrides = []): array
    {
        $timeout = max(1, (int) (config('firebase.request_timeout', 5) * 1000));

        return array_replace_recursive([
            'timeoutMillis' => $timeout,
            'retrySettings' => [
                'retriesEnabled' => false,
                'noRetriesRpcTimeoutMillis' => $timeout,
            ],
        ], $overrides);
    }

    private function openReadCircuit(Throwable $exception, string $collection): void
    {
        $this->readCircuitOpen = true;
        Cache::store('file')->put(
            $this->circuitCacheKey(),
            true,
            config('firebase.circuit_ttl', 30)
        );

        Log::warning('Firebase read failed; serving cached data for this request.', [
            'collection' => $collection,
            'error' => $exception->getMessage(),
        ]);
    }

    private function cachedFallback(string $cacheKey, mixed $default): mixed
    {
        $cache = Cache::store('file');

        if ($cache->has($cacheKey)) {
            return $cache->get($cacheKey);
        }

        return $cache->get($this->staleCacheKey($cacheKey), $default);
    }

    private function cacheAddedDocument(string $collection, string $documentId, array $data): void
    {
        $document = array_merge($data, ['id' => $documentId]);
        $documents = $this->cachedCollection($collection) ?? [];
        $documents = array_values(array_filter(
            $documents,
            fn (array $item): bool => ($item['id'] ?? null) !== $documentId
        ));
        array_unshift($documents, $document);

        $this->cacheDocument($collection, $documentId, $document);
        $this->cacheCollection($collection, $documents);
        $this->closeReadCircuit();
    }

    private function cacheChangedDocument(
        string $collection,
        string $documentId,
        array $data,
        bool $merge
    ): void {
        $documents = $this->cachedCollection($collection) ?? [];
        $existing = $this->cachedDocument($collection, $documentId)
            ?? $this->findDocument($documents, $documentId)
            ?? [];
        $document = $merge ? array_replace($existing, $data) : $data;
        $document['id'] = $documentId;

        $replaced = false;
        foreach ($documents as $index => $item) {
            if (($item['id'] ?? null) === $documentId) {
                $documents[$index] = $document;
                $replaced = true;
                break;
            }
        }
        if (! $replaced) {
            array_unshift($documents, $document);
        }

        $this->cacheDocument($collection, $documentId, $document);
        $this->cacheCollection($collection, array_values($documents));
        $this->closeReadCircuit();
    }

    private function cacheDeletedDocument(string $collection, string $documentId): void
    {
        $cache = Cache::store('file');
        $documentKey = $this->documentCacheKey($collection, $documentId);
        $cache->forget($documentKey);
        $cache->forget($this->staleCacheKey($documentKey));

        $documents = array_values(array_filter(
            $this->cachedCollection($collection) ?? [],
            fn (array $item): bool => ($item['id'] ?? null) !== $documentId
        ));
        $this->cacheCollection($collection, $documents);
        $this->closeReadCircuit();
    }

    private function cacheCollection(string $collection, array $documents): void
    {
        foreach (self::COLLECTION_LIMITS as $limit) {
            $data = $limit === null ? $documents : array_slice($documents, 0, $limit);
            $this->cacheCollectionVariant($collection, $limit, $data);
        }
    }

    private function cacheCollectionVariant(string $collection, ?int $limit, array $data): void
    {
        $cache = Cache::store('file');
        $key = $this->collectionCacheKey($collection, $limit);
        $cache->put($key, $data, config('firebase.cache_ttl', 300));
        $cache->forever($this->staleCacheKey($key), $data);
    }

    private function cachedCollection(string $collection, bool $includeStale = true): ?array
    {
        $cache = Cache::store('file');

        foreach (self::COLLECTION_LIMITS as $limit) {
            $key = $this->collectionCacheKey($collection, $limit);
            if ($cache->has($key)) {
                return $cache->get($key);
            }
        }

        if ($includeStale) {
            foreach (self::COLLECTION_LIMITS as $limit) {
                $key = $this->collectionCacheKey($collection, $limit);
                if ($cache->has($this->staleCacheKey($key))) {
                    return $cache->get($this->staleCacheKey($key));
                }
            }
        }

        return null;
    }

    private function cachedDocument(string $collection, string $documentId): ?array
    {
        $cache = Cache::store('file');
        $key = $this->documentCacheKey($collection, $documentId);
        $document = $this->cachedFallback($key, null);

        return is_array($document) ? $document : null;
    }

    private function findDocumentInCachedCollection(
        string $collection,
        string $documentId,
        bool $includeStale = true
    ): ?array {
        return $this->findDocument(
            $this->cachedCollection($collection, $includeStale) ?? [],
            $documentId
        );
    }

    private function findDocument(array $documents, string $documentId): ?array
    {
        foreach ($documents as $document) {
            if (($document['id'] ?? null) === $documentId) {
                return $document;
            }
        }

        return null;
    }

    private function cacheDocument(string $collection, string $documentId, array $data): void
    {
        $cache = Cache::store('file');
        $key = $this->documentCacheKey($collection, $documentId);
        $data['id'] = $documentId;
        $cache->put($key, $data, config('firebase.cache_ttl', 300));
        $cache->forever($this->staleCacheKey($key), $data);
    }

    private function closeReadCircuit(): void
    {
        $this->readCircuitOpen = false;
        Cache::store('file')->forget($this->circuitCacheKey());
    }

    /**
     * Keep CRUD usable during temporary Google API outages. Authorization,
     * validation, and other permanent failures are still surfaced normally.
     */
    private function performWrite(callable $write, array $pendingMutation): void
    {
        try {
            if ($this->hasPendingWriteFor($pendingMutation)) {
                $this->flushPendingWrites();
            }
            $write();
            $this->discardPendingWrite($pendingMutation);
            $this->flushPendingWrites();
        } catch (Throwable $exception) {
            if (! $this->isTransientNetworkFailure($exception)) {
                throw $exception;
            }

            $this->queuePendingWrite($pendingMutation);
            Log::warning('Firebase write queued locally after a network failure.', [
                'operation' => $pendingMutation['operation'],
                'collection' => $pendingMutation['collection'],
                'document_id' => $pendingMutation['document_id'],
                'error' => $exception->getMessage(),
            ]);
        }
    }

    private function queuePendingWrite(array $mutation): void
    {
        $cache = Cache::store('file');
        $pending = $cache->get($this->pendingWritesCacheKey(), []);
        $key = $mutation['collection'].'/'.$mutation['document_id'];
        $previous = $pending[$key] ?? null;

        if (
            is_array($previous)
            && ($previous['operation'] ?? null) === 'set'
            && ($mutation['operation'] ?? null) === 'set'
        ) {
            $mutation['data'] = array_replace(
                $previous['data'] ?? [],
                $mutation['data'] ?? []
            );
            $mutation['merge'] = $previous['merge'] ?? $mutation['merge'] ?? true;
        }

        $pending[$key] = $mutation;
        $cache->forever($this->pendingWritesCacheKey(), $pending);
    }

    private function flushPendingWrites(): void
    {
        $cache = Cache::store('file');
        $pending = $cache->get($this->pendingWritesCacheKey(), []);

        foreach ($pending as $key => $mutation) {
            try {
                $document = $this->firestore()
                    ->collection($mutation['collection'])
                    ->document($mutation['document_id']);

                if ($mutation['operation'] === 'delete') {
                    $document->delete($this->writeOptions());
                } else {
                    $document->set(
                        $mutation['data'],
                        $this->writeOptions(['merge' => $mutation['merge'] ?? true])
                    );
                }

                unset($pending[$key]);
            } catch (Throwable $exception) {
                if (! $this->isTransientNetworkFailure($exception)) {
                    Log::error('A queued Firebase write could not be synchronized.', [
                        'mutation' => $key,
                        'error' => $exception->getMessage(),
                    ]);
                }
                break;
            }
        }

        if ($pending === []) {
            $cache->forget($this->pendingWritesCacheKey());
        } else {
            $cache->forever($this->pendingWritesCacheKey(), $pending);
        }
    }

    private function isTransientNetworkFailure(Throwable $exception): bool
    {
        if ($exception instanceof ConnectException) {
            return true;
        }

        $message = strtolower($exception->getMessage());

        return str_contains($message, 'curl error 7')
            || str_contains($message, 'curl error 28')
            || str_contains($message, 'deadline exceeded')
            || str_contains($message, 'temporarily unavailable')
            || str_contains($message, 'unavailable');
    }

    private function pendingWritesCacheKey(): string
    {
        return 'firebase.pending-writes';
    }

    private function hasPendingWrites(): bool
    {
        return Cache::store('file')->has($this->pendingWritesCacheKey());
    }

    private function hasPendingWriteFor(array $mutation): bool
    {
        $pending = Cache::store('file')->get($this->pendingWritesCacheKey(), []);
        $key = $mutation['collection'].'/'.$mutation['document_id'];

        return isset($pending[$key]);
    }

    private function discardPendingWrite(array $mutation): void
    {
        $cache = Cache::store('file');
        $pending = $cache->get($this->pendingWritesCacheKey(), []);
        $key = $mutation['collection'].'/'.$mutation['document_id'];
        unset($pending[$key]);

        if ($pending === []) {
            $cache->forget($this->pendingWritesCacheKey());
        } else {
            $cache->forever($this->pendingWritesCacheKey(), $pending);
        }
    }

    private function staleCacheKey(string $cacheKey): string
    {
        return "{$cacheKey}.stale";
    }

    private function isReadCircuitOpen(): bool
    {
        return $this->readCircuitOpen
            || Cache::store('file')->has($this->circuitCacheKey());
    }

    private function circuitCacheKey(): string
    {
        return 'firebase.read.circuit-open';
    }

    private function writeOptions(array $overrides = []): array
    {
        $timeout = max(1, (int) (config('firebase.write_timeout', 15) * 1000));

        return array_replace_recursive([
            'timeoutMillis' => $timeout,
            'retrySettings' => [
                'retriesEnabled' => false,
                'noRetriesRpcTimeoutMillis' => $timeout,
            ],
        ], $overrides);
    }

    private function collectionCacheKey(string $collection, ?int $limit = null): string
    {
        return "firebase.collection.{$collection}.".($limit ?? 'all');
    }

    private function documentCacheKey(string $collection, string $documentId): string
    {
        return "firebase.document.{$collection}.{$documentId}";
    }
}
