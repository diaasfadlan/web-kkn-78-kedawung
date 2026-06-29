<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Show edit contact form
     */
    public function edit(string $id = 'main'): View
    {
        $contact = $this->firebase->getDocument('contact', $id);
        
        return view('admin.contact.edit', [
            'contact' => $contact ?? [],
            'id' => $id,
        ]);
    }

    /**
     * Update contact information
     */
    public function update(Request $request, string $id = 'main'): RedirectResponse
    {
        $validated = $request->validate([
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|email',
            'instagram' => 'nullable|url',
            'tiktok' => 'nullable|url',
            'whatsapp' => 'nullable|string',
            'map_url' => 'nullable|url',
        ]);

        try {
            $data = [
                'address' => $validated['address'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'instagram' => $validated['instagram'] ?? '',
                'tiktok' => $validated['tiktok'] ?? '',
                'whatsapp' => $validated['whatsapp'] ?? '',
                'map_url' => $validated['map_url'] ?? '',
            ];

            $this->firebase->setDocument('contact', $id, $data, true);

            return redirect()->back()
                ->with('success', 'Informasi kontak berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui kontak: ' . $e->getMessage());
        }
    }
}
