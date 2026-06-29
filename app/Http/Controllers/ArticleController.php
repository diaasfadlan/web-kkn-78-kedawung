<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ArticleController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display list of articles
     */
    public function index(): View
    {
        $articles = $this->firebase->getCollection('articles');

        return view('admin.artikel.index', ['articles' => $articles]);
    }

    /**
     * Show create article form
     */
    public function create(): View
    {
        return view('admin.artikel.create');
    }

    /**
     * Store article in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'author' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $storedPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $storedPath = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'slug' => str()->slug($validated['title']),
                'content' => $validated['content'],
                'category' => $validated['category'],
                'author' => $validated['author'],
                'published_at' => now()->toIso8601String(),
                'thumbnail_url' => $storedPath ? '/storage/'.$storedPath : '',
                'gallery' => [],
            ];

            $this->firebase->addDocument('articles', $data);

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            report($e);

            return back()->withInput()->with('error', 'Gagal menambahkan artikel: '.$e->getMessage());
        }
    }

    /**
     * Show edit article form
     */
    public function edit(string $id): View
    {
        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);

        return view('admin.artikel.edit', ['article' => $article, 'id' => $id]);
    }

    /**
     * Update article
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string',
            'author' => 'required|string',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);
        $newThumbnailPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $newThumbnailPath = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'slug' => str()->slug($validated['title']),
                'content' => $validated['content'],
                'category' => $validated['category'],
                'author' => $validated['author'],
            ];

            if ($newThumbnailPath) {
                $data['thumbnail_url'] = '/storage/'.$newThumbnailPath;
            }

            $this->firebase->updateDocument('articles', $id, $data);

            if ($newThumbnailPath) {
                $this->deleteLocalThumbnail($article['thumbnail_url'] ?? null);
            }

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newThumbnailPath) {
                Storage::disk('public')->delete($newThumbnailPath);
            }
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui artikel: '.$e->getMessage());
        }
    }

    /**
     * Delete article
     */
    public function destroy(string $id): RedirectResponse
    {
        $article = $this->firebase->getDocument('articles', $id);
        abort_if(! $article, 404);

        try {
            $this->firebase->deleteDocument('articles', $id);
            $this->deleteLocalThumbnail($article['thumbnail_url'] ?? null);

            return redirect()->route('artikel.index')
                ->with('success', 'Artikel berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus artikel: '.$e->getMessage());
        }
    }

    private function storeThumbnail(Request $request): string
    {
        $file = $request->file('thumbnail');
        $filename = uniqid('article_', true).'.'.$file->extension();
        $path = $file->storePubliclyAs('articles', $filename, 'public');

        if (! $path) {
            throw new \RuntimeException('Thumbnail artikel gagal disimpan.');
        }

        return $path;
    }

    private function deleteLocalThumbnail(?string $url): void
    {
        if (! $url) {
            return;
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));
        $position = strpos($path, '/storage/');
        if ($position !== false) {
            Storage::disk('public')->delete(substr($path, $position + strlen('/storage/')));
        }
    }
}
