<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class GroupProfileController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display list of members
     */
    public function index(): View
    {
        $members = $this->firebase->getCollection('members');
        return view('admin.group.index', ['members' => $members]);
    }

    /**
     * Show create member form
     */
    public function create(): View
    {
        return view('admin.group.create');
    }

    /**
     * Store member in database
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:20',
            'prodi' => 'required|string',
            'position' => 'required|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $storedPath = null;

        try {
            if ($request->hasFile('photo')) {
                $storedPath = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'photo_url' => $storedPath ? '/storage/' . $storedPath : '',
                'social_media' => [
                    'email' => $validated['email'] ?? '',
                    'instagram' => $validated['instagram'] ?? '',
                    'whatsapp' => $validated['whatsapp'] ?? '',
                ],
            ];

            $this->firebase->addDocument('members', $data);

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil ditambahkan');
        } catch (\Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            report($e);
            return back()->with('error', 'Gagal menambahkan anggota: ' . $e->getMessage());
        }
    }

    /**
     * Show edit member form
     */
    public function edit(string $id): View
    {
        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);

        return view('admin.group.edit', ['member' => $member, 'id' => $id]);
    }

    /**
     * Update member
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prodi' => 'required|string',
            'position' => 'required|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);
        $newPhotoPath = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoPath = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'social_media' => [
                    'email' => $validated['email'] ?? '',
                    'instagram' => $validated['instagram'] ?? '',
                    'whatsapp' => $validated['whatsapp'] ?? '',
                ],
            ];

            if ($newPhotoPath) {
                $data['photo_url'] = '/storage/' . $newPhotoPath;
            }

            $this->firebase->updateDocument('members', $id, $data);

            if ($newPhotoPath) {
                $this->deleteLocalPhoto($member['photo_url'] ?? null);
            }

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }
            report($e);
            return back()->with('error', 'Gagal memperbarui anggota: ' . $e->getMessage());
        }
    }

    /**
     * Delete member
     */
    public function destroy(string $id): RedirectResponse
    {
        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);

        try {
            $this->firebase->deleteDocument('members', $id);
            $this->deleteLocalPhoto($member['photo_url'] ?? null);

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }

    private function storePhoto(Request $request): string
    {
        $file = $request->file('photo');
        $filename = uniqid('member_', true) . '.' . $file->extension();
        $path = $file->storePubliclyAs('members', $filename, 'public');

        if (!$path) {
            throw new \RuntimeException('Foto anggota gagal disimpan.');
        }

        return $path;
    }

    private function deleteLocalPhoto(?string $url): void
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
