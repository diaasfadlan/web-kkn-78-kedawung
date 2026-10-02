<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use App\Services\ImageCompressorService;
use App\Services\MediaStorageService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class GroupProfileController extends Controller
{
    protected FirebaseService $firebase;

    protected MediaStorageService $mediaStorage;

    protected ImageCompressorService $compressor;

    public function __construct(FirebaseService $firebase, MediaStorageService $mediaStorage, ImageCompressorService $compressor)
    {
        $this->firebase = $firebase;
        $this->mediaStorage = $mediaStorage;
        $this->compressor = $compressor;
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
            'photo' => 'nullable|image',
        ]);

        $storedUpload = null;

        try {
            if ($request->hasFile('photo')) {
                $storedUpload = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'photo_url' => $storedUpload['url'] ?? '',
                'photo_public_id' => $storedUpload['public_id'] ?? '',
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
            if ($storedUpload) {
                $this->mediaStorage->deleteByUrl($storedUpload['url'] ?? null);
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
            'nim' => 'required|string|max:20',
            'prodi' => 'required|string',
            'position' => 'required|string',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'photo' => 'nullable|image',
        ]);

        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);
        $newPhotoUpload = null;

        try {
            if ($request->hasFile('photo')) {
                $newPhotoUpload = $this->storePhoto($request);
            }

            $data = [
                'name' => $validated['name'],
                'nim' => $validated['nim'],
                'prodi' => $validated['prodi'],
                'position' => $validated['position'],
                'social_media' => [
                    'email' => $validated['email'] ?? '',
                    'instagram' => $validated['instagram'] ?? '',
                    'whatsapp' => $validated['whatsapp'] ?? '',
                ],
            ];

            if ($newPhotoUpload) {
                $data['photo_url'] = $newPhotoUpload['url'];
                $data['photo_public_id'] = $newPhotoUpload['public_id'];
            }

            $this->firebase->updateDocument('members', $id, $data);

            if ($newPhotoUpload) {
                $this->mediaStorage->deleteUploadedAsset(
                    $member['photo_public_id'] ?? null,
                    $member['photo_url'] ?? null
                );
            }

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil diperbarui');
        } catch (\Throwable $e) {
            if ($newPhotoUpload) {
                $this->mediaStorage->deleteByUrl($newPhotoUpload['url'] ?? null);
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
            $this->mediaStorage->deleteUploadedAsset(
                $member['photo_public_id'] ?? null,
                $member['photo_url'] ?? null
            );

            return redirect()->route('group.index')
                ->with('success', 'Anggota berhasil dihapus');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus anggota: ' . $e->getMessage());
        }
    }

    private function storePhoto(Request $request): array
    {
        $file = $this->compressor->compress($request->file('photo'));

        return $this->mediaStorage->uploadPublicFile($file, 'members', 'member');
    }

    /**
     * Show form for editing group identity (group_profile) and lecturer (lecturers)
     */
    public function setting(): View
    {
        $group = $this->firebase->getDocument('group_profile', 'main') ?? [];
        $lecturer = $this->firebase->getDocument('lecturers', 'main') ?? [];

        return view('admin.group.setting', [
            'group' => $group,
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Update group profile and lecturer information
     */
    public function updateSetting(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'group_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'period' => 'required|string|max:255',
            'university' => 'required|string|max:255',
            'description' => 'required|string',
            'group_photo' => 'nullable|image',
            'lecturer_name' => 'nullable|string|max:255',
            'lecturer_nidn' => 'nullable|string|max:50',
            'lecturer_department' => 'nullable|string|max:255',
            'lecturer_photo' => 'nullable|image',
        ]);

        $group = $this->firebase->getDocument('group_profile', 'main') ?? [];
        $lecturer = $this->firebase->getDocument('lecturers', 'main') ?? [];

        $newGroupPhoto = null;
        $newLecturerPhoto = null;

        try {
            if ($request->hasFile('group_photo')) {
                $compressed = $this->compressor->compress($request->file('group_photo'));
                $newGroupPhoto = $this->mediaStorage->uploadPublicFile($compressed, 'group', 'group_banner');
            }

            if ($request->hasFile('lecturer_photo')) {
                $compressed = $this->compressor->compress($request->file('lecturer_photo'));
                $newLecturerPhoto = $this->mediaStorage->uploadPublicFile($compressed, 'lecturers', 'dpl');
            }

            $groupData = [
                'name' => $validated['group_name'],
                'location' => $validated['location'],
                'period' => $validated['period'],
                'university' => $validated['university'],
                'description' => $validated['description'],
            ];

            if ($newGroupPhoto) {
                $groupData['photo_url'] = $newGroupPhoto['url'];
                $groupData['photo_public_id'] = $newGroupPhoto['public_id'];
            }

            $this->firebase->setDocument('group_profile', 'main', $groupData, true);

            if ($newGroupPhoto) {
                $this->mediaStorage->deleteUploadedAsset($group['photo_public_id'] ?? null, $group['photo_url'] ?? null);
            }

            // Update DPL / Lecturer
            $lecturerData = [
                'name' => $validated['lecturer_name'] ?? '',
                'nidn' => $validated['lecturer_nidn'] ?? '',
                'department' => $validated['lecturer_department'] ?? '',
            ];

            if ($newLecturerPhoto) {
                $lecturerData['photo_url'] = $newLecturerPhoto['url'];
                $lecturerData['photo_public_id'] = $newLecturerPhoto['public_id'];
            }

            $this->firebase->setDocument('lecturers', 'main', $lecturerData, true);

            if ($newLecturerPhoto) {
                $this->mediaStorage->deleteUploadedAsset($lecturer['photo_public_id'] ?? null, $lecturer['photo_url'] ?? null);
            }

            return back()->with('success', 'Profil kelompok dan DPL berhasil diperbarui!');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', 'Gagal memperbarui profil kelompok: '.$e->getMessage());
        }
    }
}
