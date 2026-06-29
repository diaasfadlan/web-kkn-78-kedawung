<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GalleryController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display list of galleries
     */
    public function index(): View
    {
        $galleries = $this->firebase->getCollection('galleries');

        return view('admin.gallery.index', ['galleries' => $galleries]);
    }

    /**
     * Show create gallery form
     */
    public function create(): View
    {
        return view('admin.gallery.create');
    }

    /**
     * Show edit gallery form
     */
    public function edit(string $id): View
    {
        $gallery = $this->firebase->getDocument('galleries', $id);
        abort_if(! $gallery, 404);

        return view('admin.gallery.edit', ['gallery' => $gallery]);
    }

    /**
     * Store gallery in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:edukasi,sosialisasi,gotong-royong,dokumentasi,penutupan',
            'image' => 'required|image|max:5120',
        ]);

        $storedPath = null;

        try {
            $file = $request->file('image');
            $filename = uniqid('gallery_', true).'.'.$file->extension();
            $storedPath = $file->storePubliclyAs(
                'galleries',
                $filename,
                'public'
            );

            if (! $storedPath) {
                throw new \RuntimeException('File gambar gagal disimpan ke penyimpanan publik.');
            }

            // Store a same-origin URL so it works on any local port or production domain.
            $imageUrl = '/storage/'.ltrim($storedPath, '/');

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'category' => $validated['category'],
                'image_url' => $imageUrl,
                'created_at' => now()->toIso8601String(),
            ];

            $this->firebase->addDocument('galleries', $data);

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            report($e);

            return back()->with('error', 'Gagal menambahkan galeri: '.$e->getMessage());
        }
    }

    /**
     * Update gallery metadata and optionally replace its image.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:edukasi,sosialisasi,gotong-royong,dokumentasi,penutupan',
            'image' => 'nullable|image|max:5120',
        ]);

        $newPath = null;

        try {
            $gallery = $this->firebase->getDocument('galleries', $id);
            abort_if(! $gallery, 404);

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'category' => $validated['category'],
                'updated_at' => now()->toIso8601String(),
            ];

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = uniqid('gallery_', true).'.'.$file->extension();
                $newPath = $file->storePubliclyAs('galleries', $filename, 'public');

                if (! $newPath) {
                    throw new \RuntimeException('File gambar baru gagal disimpan.');
                }

                $data['image_url'] = '/storage/'.ltrim($newPath, '/');
            }

            $this->firebase->updateDocument('galleries', $id, $data);

            if ($newPath !== null) {
                try {
                    $this->deleteImage($gallery['image_url'] ?? null);
                } catch (\Throwable $cleanupError) {
                    // The Firestore update and new image are already valid.
                    // A failed cleanup must not roll them back.
                    report($cleanupError);
                }
            }

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            report($e);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui galeri: '.$e->getMessage());
        }
    }

    /**
     * Delete gallery
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $gallery = $this->firebase->getDocument('galleries', $id);
            $this->firebase->deleteDocument('galleries', $id);
            $this->deleteImage($gallery['image_url'] ?? null);

            return redirect()->route('gallery.index')
                ->with('success', 'Galeri berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus galeri: '.$e->getMessage());
        }
    }

    private function deleteImage(?string $imageUrl): void
    {
        if (! $imageUrl) {
            return;
        }

        $urlPath = rawurldecode((string) parse_url($imageUrl, PHP_URL_PATH));
        $publicStoragePosition = strpos($urlPath, '/storage/');

        if ($publicStoragePosition !== false) {
            $localPath = substr($urlPath, $publicStoragePosition + strlen('/storage/'));
            Storage::disk('public')->delete($localPath);

            return;
        }

        $objectPath = ltrim($urlPath, '/');
        if ($objectPath !== '') {
            $this->firebase->storage()->getBucket()->object($objectPath)->delete();
        }
    }
}
