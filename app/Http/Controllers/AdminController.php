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

        $stats = [
            'total_articles' => count($articles),
            'total_programs' => count($workPrograms),
            'total_galleries' => count($galleries),
            'total_members' => count($members),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentArticles' => array_slice($articles, 0, 5),
            'recentPrograms' => array_slice($workPrograms, 0, 5),
        ]);
    }
}
