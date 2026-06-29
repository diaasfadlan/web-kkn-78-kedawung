<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TimelineController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display list of timelines
     */
    public function index(): View
    {
        $timelines = $this->firebase->getCollection('timelines');
        return view('admin.timeline.index', ['timelines' => $timelines]);
    }

    /**
     * Show create timeline form
     */
    public function create(): View
    {
        return view('admin.timeline.create');
    }

    /**
     * Store timeline in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => $validated['date'],
                'icon' => $validated['icon'] ?? 'fa-circle',
                'color' => $validated['color'] ?? '#3498db',
            ];

            $this->firebase->addDocument('timelines', $data);

            return redirect()->route('timeline.index')
                ->with('success', 'Timeline berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan timeline: ' . $e->getMessage());
        }
    }

    /**
     * Show edit timeline form
     */
    public function edit(string $id): View
    {
        $timeline = $this->firebase->getDocument('timelines', $id);
        abort_if(!$timeline, 404);

        return view('admin.timeline.edit', ['timeline' => $timeline, 'id' => $id]);
    }

    /**
     * Update timeline
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'icon' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        try {
            $data = [
                'title' => $validated['title'],
                'description' => $validated['description'],
                'date' => $validated['date'],
                'icon' => $validated['icon'] ?? 'fa-circle',
                'color' => $validated['color'] ?? '#3498db',
            ];

            $this->firebase->updateDocument('timelines', $id, $data);

            return redirect()->route('timeline.index')
                ->with('success', 'Timeline berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui timeline: ' . $e->getMessage());
        }
    }

    /**
     * Delete timeline
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->firebase->deleteDocument('timelines', $id);

            return redirect()->route('timeline.index')
                ->with('success', 'Timeline berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus timeline: ' . $e->getMessage());
        }
    }
}
