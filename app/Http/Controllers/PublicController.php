<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\View\View;

class PublicController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    /**
     * Display landing page
     */
    public function index(): View
    {
        $groupProfile = $this->firebase->getDocument('group_profile', 'main');
        $members = $this->firebase->getCollection('members', 6);
        $workPrograms = $this->firebase->getCollection('work_programs', 3);
        $latestArticles = $this->firebase->getCollection('articles', 3);
        $featuredGalleries = $this->firebase->getCollection('galleries', 8);

        return view('public.index', [
            'groupProfile' => $groupProfile,
            'members' => $members,
            'workPrograms' => $workPrograms,
            'latestArticles' => $latestArticles,
            'featuredGalleries' => $featuredGalleries,
        ]);
    }

    /**
     * Display village profile page
     */
    public function profileDesa(): View
    {
        $villageProfile = $this->firebase->getDocument('village_profile', 'main');

        return view('public.profil-desa', [
            'village' => $villageProfile,
        ]);
    }

    /**
     * Display group profile page
     */
    public function profileKelompok(): View
    {
        $groupProfile = $this->firebase->getDocument('group_profile', 'main');
        $members = $this->firebase->getCollection('members');
        $lecturer = $this->firebase->getDocument('lecturers', 'main');

        return view('public.profil-kelompok', [
            'group' => $groupProfile,
            'members' => $members,
            'lecturer' => $lecturer,
        ]);
    }

    /**
     * Display an individual member as an interactive ID card.
     */
    public function memberDetail(string $id): View
    {
        $member = $this->firebase->getDocument('members', $id);
        abort_if(!$member, 404);

        return view('public.anggota-detail', [
            'member' => $member,
        ]);
    }

    /**
     * Display work programs list
     */
    public function programKerja(): View
    {
        $workPrograms = $this->firebase->getCollection('work_programs');

        return view('public.program-kerja', [
            'programs' => $workPrograms,
        ]);
    }

    /**
     * Display work program detail
     */
    public function programKerjaDetail(string $id): View
    {
        $program = $this->firebase->getDocument('work_programs', $id);

        abort_if(!$program, 404);

        return view('public.program-kerja-detail', [
            'program' => $program,
        ]);
    }

    /**
     * Display articles list
     */
    public function artikel(): View
    {
        $articles = $this->firebase->getCollection('articles');

        return view('public.artikel', [
            'articles' => $articles,
        ]);
    }

    /**
     * Display article detail
     */
    public function artikelDetail(string $id): View
    {
        $article = $this->firebase->getDocument('articles', $id);

        abort_if(!$article, 404);

        return view('public.artikel-detail', [
            'article' => $article,
        ]);
    }

    /**
     * Display gallery
     */
    public function galeri(): View
    {
        $galleries = $this->firebase->getCollection('galleries');

        return view('public.galeri', [
            'galleries' => $galleries,
        ]);
    }

    /**
     * Display timeline
     */
    public function timeline(): View
    {
        $timelines = $this->firebase->getCollection('timelines');

        return view('public.timeline', [
            'timelines' => $timelines,
        ]);
    }

    /**
     * Display contact page
     */
    public function kontak(): View
    {
        $contact = $this->firebase->getDocument('contact', 'main');

        return view('public.kontak', [
            'contact' => $contact,
        ]);
    }
}
