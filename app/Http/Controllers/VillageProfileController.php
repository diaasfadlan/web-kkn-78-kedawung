<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VillageProfileController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Show edit village profile form
     */
    public function edit(string $id = 'main'): View
    {
        $village = $this->firebase->getDocument('village_profile', $id);
        
        return view('admin.village.edit', [
            'village' => $village ?? [],
            'id' => $id,
        ]);
    }

    /**
     * Update village profile
     */
    public function update(Request $request, string $id = 'main'): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'district' => 'required|string',
            'regency' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'nullable|string',
            'history' => 'nullable|string',
            'philosophy' => 'nullable|string',
            'demographics' => 'nullable|string',
            'potential' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_address' => 'nullable|string',
        ]);

        try {
            $data = [
                'name' => $validated['name'],
                'address' => $validated['address'],
                'district' => $validated['district'],
                'regency' => $validated['regency'],
                'province' => $validated['province'],
                'postal_code' => $validated['postal_code'] ?? '',
                'history' => $validated['history'] ?? '',
                'philosophy' => $validated['philosophy'] ?? '',
                'demographics' => $validated['demographics'] ?? '',
                'potential' => $validated['potential'] ?? '',
                'contact_phone' => $validated['contact_phone'] ?? '',
                'contact_email' => $validated['contact_email'] ?? '',
                'contact_address' => $validated['contact_address'] ?? '',
            ];

            $this->firebase->setDocument('village_profile', $id, $data, true);

            return redirect()->back()
                ->with('success', 'Profil desa berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui profil desa: ' . $e->getMessage());
        }
    }
}
