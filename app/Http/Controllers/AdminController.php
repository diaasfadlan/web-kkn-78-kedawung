<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\View\View;

class AdminController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display admin dashboard
     */
    public function dashboard(): View
    {
        $articles = $this->firebase->getCollection('articles');
        $workPrograms = $this->firebase->getCollection('work_programs');
        $galleries = $this->firebase->getCollection('galleries');
        $members = $this->firebase->getCollection('members');

        $messages = $this->firebase->getCollection('messages');

        // Urutkan artikel dari yang paling baru
        usort($articles, fn($a, $b) => strcmp((string)($b['published_at'] ?? ''), (string)($a['published_at'] ?? '')));
        usort($workPrograms, fn($a, $b) => strcmp((string)($b['start_date'] ?? ''), (string)($a['start_date'] ?? '')));
        usort($messages, fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));

        $stats = [
            'total_articles' => count($articles),
            'total_programs' => count($workPrograms),
            'total_galleries' => count($galleries),
            'total_members' => count($members),
            'total_messages' => count($messages),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentArticles' => array_slice($articles, 0, 5),
            'recentPrograms' => array_slice($workPrograms, 0, 5),
            'recentMessages' => array_slice($messages, 0, 5),
        ]);
    }

    /**
     * Display inbox messages from public contact form
     */
    public function messages(): View
    {
        $messages = $this->firebase->getCollection('messages');
        usort($messages, fn($a, $b) => strcmp((string)($b['created_at'] ?? ''), (string)($a['created_at'] ?? '')));

        return view('admin.messages.index', [
            'messages' => $messages,
        ]);
    }

    /**
     * Delete a contact message
     */
    public function destroyMessage(string $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $this->firebase->deleteDocument('messages', $id);

            return back()->with('success', 'Pesan berhasil dihapus.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal menghapus pesan: '.$e->getMessage());
        }
    }

    /**
     * Toggle read / unread status of a message
     */
    public function toggleMessageRead(string $id): \Illuminate\Http\RedirectResponse
    {
        try {
            $message = $this->firebase->getDocument('messages', $id);
            if ($message) {
                $isRead = ! ($message['is_read'] ?? false);
                $this->firebase->updateDocument('messages', $id, ['is_read' => $isRead]);
            }

            return back()->with('success', 'Status pesan diperbarui.');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Gagal memperbarui status: '.$e->getMessage());
        }
    }
}
