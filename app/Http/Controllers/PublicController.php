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
        $workPrograms = $this->firebase->getCollection('work_programs');
        $articles = $this->firebase->getCollection('articles');
        $featuredGalleries = $this->firebase->getCollection('galleries', 8);

        // Urutkan artikel dari yang paling baru
        usort($articles, function ($a, $b) {
            return strcmp((string) ($b['published_at'] ?? ''), (string) ($a['published_at'] ?? ''));
        });

        // Urutkan program kerja
        usort($workPrograms, function ($a, $b) {
            return strcmp((string) ($b['start_date'] ?? ''), (string) ($a['start_date'] ?? ''));
        });

        return view('public.index', [
            'groupProfile' => $groupProfile,
            'members' => $members,
            'workPrograms' => array_slice($workPrograms, 0, 3),
            'latestArticles' => array_slice($articles, 0, 3),
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

        $positionAliases = [
            'KORDES' => ['KORDES', 'KETUA', 'KOORDINATOR DESA', 'KETUA KELOMPOK'],
            'SEKRETARIS' => ['SEKRETARIS', 'SEKERTARIS'],
            'BENDAHARA' => ['BENDAHARA'],
            'HUMAS' => ['HUMAS', 'HUBUNGAN MASYARAKAT'],
            'ACARA' => ['ACARA', 'DIVISI ACARA'],
            'LOGISTIK' => ['LOGISTIK', 'PERLENGKAPAN', 'DIVISI LOGISTIK'],
            'PDD' => ['PDD', 'DOKUMENTASI', 'PUBLIKASI', 'DIVISI PDD'],
        ];

        $organization = collect($positionAliases)->map(function (array $aliases) use ($members): array {
            return collect($members)->filter(function (array $member) use ($aliases): bool {
                $pos = mb_strtoupper(trim((string) ($member['position'] ?? '')));
                return in_array($pos, $aliases, true);
            })->values()->all();
        })->all();

        return view('public.profil-kelompok', [
            'group' => $groupProfile,
            'members' => $members,
            'lecturer' => $lecturer,
            'organization' => $organization,
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

        usort($workPrograms, function ($a, $b) {
            return strcmp((string) ($b['start_date'] ?? ''), (string) ($a['start_date'] ?? ''));
        });

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

        usort($articles, function ($a, $b) {
            return strcmp((string) ($b['published_at'] ?? ''), (string) ($a['published_at'] ?? ''));
        });

        $categories = collect($articles)
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('public.artikel', [
            'articles' => $articles,
            'categories' => $categories,
        ]);
    }

    /**
     * Display article detail
     */
    public function artikelDetail(string $id): View
    {
        $article = $this->firebase->getDocument('articles', $id);

        abort_if(!$article, 404);

        $allArticles = $this->firebase->getCollection('articles');

        $filtered = collect($allArticles)
            ->filter(fn($item) => ($item['id'] ?? '') !== $id)
            ->sortByDesc(fn($item) => $item['published_at'] ?? '')
            ->values();

        $categoryMatches = $filtered
            ->filter(fn($item) => !empty($article['category']) && ($item['category'] ?? '') === $article['category'])
            ->take(3)
            ->values();

        $relatedArticles = $categoryMatches->isNotEmpty() ? $categoryMatches->all() : $filtered->take(3)->all();

        return view('public.artikel-detail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    /**
     * Display gallery
     */
    public function galeri(): View
    {
        $galleries = $this->firebase->getCollection('galleries');

        usort($galleries, function ($a, $b) {
            return strcmp((string) ($b['created_at'] ?? ''), (string) ($a['created_at'] ?? ''));
        });

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

        // Urutkan timeline secara kronologis (tanggal awal ke akhir)
        usort($timelines, function ($a, $b) {
            return strcmp((string) ($a['date'] ?? ''), (string) ($b['date'] ?? ''));
        });

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

    /**
     * Handle public contact form submission
     */
    public function kirimPesan(\Illuminate\Http\Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:3000',
        ]);

        try {
            $this->firebase->addDocument('messages', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? '',
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'created_at' => now()->toIso8601String(),
                'is_read' => false,
            ]);

            return back()->with('success_message', 'Terima kasih! Pesan Anda telah berhasil dikirim.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error_message', 'Maaf, terjadi kendala saat mengirim pesan. Silakan coba kembali.');
        }
    }
}
