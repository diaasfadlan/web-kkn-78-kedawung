<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class WorkProgramController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display list of work programs
     */
    public function index(): View
    {
        $programs = $this->firebase->getCollection('work_programs');
        return view('admin.program.index', ['programs' => $programs]);
    }

    /**
     * Show create program form
     */
    public function create(): View
    {
        return view('admin.program.create');
    }

    /**
     * Store program in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objective' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $storedPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $storedPath = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'objective' => $validated['objective'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
                'thumbnail_url' => $storedPath ? '/storage/'.$storedPath : '',
                'gallery' => [],
            ];

            $this->firebase->addDocument('work_programs', $data);

            return redirect()->route('program.index')
                ->with('success', 'Program kerja berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            report($e);
            return back()->with('error', 'Gagal menambahkan program: ' . $e->getMessage());
        }
    }

    /**
     * Show edit program form
     */
    public function edit(string $id): View
    {
        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);

        return view('admin.program.edit', ['program' => $program, 'id' => $id]);
    }

    /**
     * Update program
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objective' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:planned,ongoing,completed',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);
        $newThumbnailPath = null;

        try {
            if ($request->hasFile('thumbnail')) {
                $newThumbnailPath = $this->storeThumbnail($request);
            }

            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'objective' => $validated['objective'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'status' => $validated['status'],
            ];

            if ($newThumbnailPath) {
                $data['thumbnail_url'] = '/storage/'.$newThumbnailPath;
            }

            $this->firebase->updateDocument('work_programs', $id, $data);

            if ($newThumbnailPath) {
                $this->deleteLocalThumbnail($program['thumbnail_url'] ?? null);
            }

            return redirect()->route('program.index')
                ->with('success', 'Program berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newThumbnailPath) {
                Storage::disk('public')->delete($newThumbnailPath);
            }
            report($e);
            return back()->with('error', 'Gagal memperbarui program: ' . $e->getMessage());
        }
    }

    /**
     * Delete program
     */
    public function destroy(string $id): RedirectResponse
    {
        $program = $this->firebase->getDocument('work_programs', $id);
        abort_if(!$program, 404);

        try {
            $this->firebase->deleteDocument('work_programs', $id);
            $this->deleteLocalThumbnail($program['thumbnail_url'] ?? null);

            return redirect()->route('program.index')
                ->with('success', 'Program berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus program: ' . $e->getMessage());
        }
    }

    private function storeThumbnail(Request $request): string
    {
        $file = $request->file('thumbnail');
        $filename = uniqid('program_', true).'.'.$file->extension();
        $path = $file->storePubliclyAs('programs', $filename, 'public');

        if (!$path) {
            throw new \RuntimeException('Thumbnail program gagal disimpan.');
        }

        return $path;
    }

    private function deleteLocalThumbnail(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = rawurldecode((string) parse_url($url, PHP_URL_PATH));
        $position = strpos($path, '/storage/');
        if ($position !== false) {
            Storage::disk('public')->delete(substr($path, $position + strlen('/storage/')));
        }
    }
}
