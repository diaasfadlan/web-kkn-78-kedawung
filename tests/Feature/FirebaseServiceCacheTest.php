<?php

namespace Tests\Feature;

use App\Services\FirebaseService;
use Illuminate\Support\Facades\Cache;
use RuntimeException;
use Tests\TestCase;

class FirebaseServiceCacheTest extends TestCase
{
    private string $collection;

    private mixed $originalPendingWrites;

    private mixed $originalCircuit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->collection = 'test_crud_'.str()->random(12);
        $this->originalPendingWrites = Cache::store('file')->get('firebase.pending-writes');
        $this->originalCircuit = Cache::store('file')->get('firebase.read.circuit-open');
        Cache::store('file')->forget('firebase.pending-writes');
        Cache::store('file')->forget('firebase.read.circuit-open');
    }

    protected function tearDown(): void
    {
        $cache = Cache::store('file');
        foreach ([null, 3, 6, 8] as $limit) {
            $key = 'firebase.collection.'.$this->collection.'.'.($limit ?? 'all');
            $cache->forget($key);
            $cache->forget($key.'.stale');
        }
        $this->restoreCacheValue('firebase.pending-writes', $this->originalPendingWrites);
        $this->restoreCacheValue('firebase.read.circuit-open', $this->originalCircuit);

        parent::tearDown();
    }

    private function restoreCacheValue(string $key, mixed $value): void
    {
        $cache = Cache::store('file');
        if ($value === null) {
            $cache->forget($key);
        } else {
            $cache->forever($key, $value);
        }
    }

    public function test_crud_remains_visible_when_firestore_network_is_unavailable(): void
    {
        $service = new TestFirebaseService(new FailingFirestore);

        $id = $service->addDocument($this->collection, ['title' => 'Program awal']);

        $this->assertSame('Program awal', $service->getCollection($this->collection)[0]['title']);
        $this->assertSame($id, $service->getCollection($this->collection, 3)[0]['id']);
        $this->assertSame('Program awal', $service->getDocument($this->collection, $id)['title']);

        $service->updateDocument($this->collection, $id, ['title' => 'Program diperbarui']);

        $this->assertSame('Program diperbarui', $service->getCollection($this->collection)[0]['title']);
        $this->assertSame('Program diperbarui', $service->getDocument($this->collection, $id)['title']);

        $service->deleteDocument($this->collection, $id);

        $this->assertSame([], $service->getCollection($this->collection));
        $this->assertNull($service->getDocument($this->collection, $id));
    }
}

class TestFirebaseService extends FirebaseService
{
    public function __construct(private readonly object $fakeFirestore) {}

    public function firestore(): mixed
    {
        return $this->fakeFirestore;
    }
}

class FailingFirestore
{
    public function collection(string $name): FailingCollection
    {
        return new FailingCollection($name);
    }
}

class FailingCollection
{
    public function __construct(private readonly string $collection) {}

    public function newDocument(): FailingDocument
    {
        return new FailingDocument($this->collection, 'local-document-id');
    }

    public function document(string $id): FailingDocument
    {
        return new FailingDocument($this->collection, $id);
    }
}

class FailingDocument
{
    public function __construct(
        private readonly string $collection,
        private readonly string $id,
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function set(array $data, array $options = []): never
    {
        throw $this->networkFailure();
    }

    public function update(array $data, array $options = []): never
    {
        throw $this->networkFailure();
    }

    public function delete(array $options = []): never
    {
        throw $this->networkFailure();
    }

    private function networkFailure(): RuntimeException
    {
        return new RuntimeException(
            'cURL error 28: Failed to connect to firestore.googleapis.com for '.$this->collection
        );
    }
}
