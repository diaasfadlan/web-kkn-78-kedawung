@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="hero home-hero">
    <div class="container">
        <div class="row align-items-center g-4 g-xl-5">
            <div class="col-lg-6">
                <span class="hero-kicker"><i class="fa-solid fa-location-dot"></i> Dari kampus untuk desa</span>
                <h1>Ide muda.<br>Karya nyata.</h1>
                <p class="lead">Kami hadir untuk belajar bersama warga, mengolah potensi lokal, dan menciptakan program yang terus berdampak bahkan setelah KKN selesai.</p>
                <div class="hero-actions">
                    <a href="{{ route('program-kerja') }}" class="btn btn-primary btn-lg">
                        Jelajahi Program <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('profil-kelompok') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-users"></i> Kenalan dengan Tim
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                @php
                    $customHeroPath = 'images/hero-main.jpg';
                    $heroImageUrl = file_exists(public_path($customHeroPath))
                        ? asset($customHeroPath)
                        : ($groupProfile['photo_url'] ?? null);
                @endphp
                <div class="hero-art" aria-label="Dokumentasi kelompok KKN">
                    <div class="hero-sticker top">Turun tangan, bukan sekadar wacana!</div>
                    <div class="hero-photo-frame">
                        @if($heroImageUrl)
                            <img src="{{ $heroImageUrl }}" alt="Foto utama kegiatan KKN" fetchpriority="high" decoding="async">
                        @else
                            <div class="hero-photo-placeholder">
                                <i class="fa-regular fa-image"></i>
                                <span>Foto utama</span>
                            </div>
                        @endif
                    </div>
                    <div class="hero-sticker bottom">KKN<br>2026</div>
                </div>
            </div>
        </div>
    </div>
</section>

@if(count($featuredGalleries ?? []) > 0)
<section class="home-gallery py-5" aria-labelledby="gallery-showcase-title">
    <div class="container">
        <div class="section-heading gallery-heading">
            <div>
                <p class="eyebrow">Potret dari lapangan</p>
                <h2 id="gallery-showcase-title">Momen dalam cerita</h2>
            </div>
            <p>Geser koleksi foto untuk melihat dokumentasi kegiatan yang telah ditambahkan oleh admin.</p>
        </div>

        <div class="coverflow" data-coverflow tabindex="0" aria-label="Carousel dokumentasi KKN">
            <div class="coverflow-stage">
                @foreach($featuredGalleries as $gallery)
                    <article class="coverflow-card" data-coverflow-card aria-hidden="{{ $loop->first ? 'false' : 'true' }}">
                        <img src="{{ $gallery['image_url'] ?? '' }}"
                             alt="{{ $gallery['title'] ?? 'Dokumentasi kegiatan KKN' }}"
                             loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                             decoding="async">
                        <div class="coverflow-caption">
                            <span>{{ $gallery['category'] ?? 'Dokumentasi' }}</span>
                            <h3>{{ $gallery['title'] ?? 'Momen KKN' }}</h3>
                        </div>
                    </article>
                @endforeach
            </div>

            @if(count($featuredGalleries) > 1)
                <button class="coverflow-control coverflow-prev" type="button" data-coverflow-prev aria-label="Foto sebelumnya">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <button class="coverflow-control coverflow-next" type="button" data-coverflow-next aria-label="Foto berikutnya">
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
                <div class="coverflow-dots" aria-label="Pilih foto">
                    @foreach($featuredGalleries as $gallery)
                        <button type="button" data-coverflow-dot="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-label="Tampilkan foto {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('galeri') }}" class="btn btn-light">Buka Galeri Lengkap <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif

<!-- Group Info Section -->
@if($groupProfile)
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 mb-4 mb-md-0">
                @if($groupProfile['photo_url'] ?? false)
                    <img src="{{ $groupProfile['photo_url'] }}" alt="Foto Kelompok" class="img-fluid rounded-3 shadow" loading="lazy" decoding="async">
                @endif
            </div>
            <div class="col-md-6">
                <h2 class="mb-3">{{ $groupProfile['name'] ?? 'Kelompok KKN' }}</h2>
                <p class="text-muted mb-2">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    <strong>Lokasi:</strong> {{ $groupProfile['location'] ?? '-' }}
                </p>
                <p class="text-muted mb-2">
                    <i class="fas fa-calendar text-primary"></i>
                    <strong>Periode:</strong> {{ $groupProfile['period'] ?? '-' }}
                </p>
                <p class="text-muted mb-4">
                    <i class="fas fa-university text-info"></i>
                    <strong>Universitas:</strong> {{ $groupProfile['university'] ?? '-' }}
                </p>
                <p class="text-justify">{{ $groupProfile['description'] ?? 'Deskripsi kelompok tidak tersedia' }}</p>
                <a href="{{ route('profil-kelompok') }}" class="btn btn-primary">
                    Lihat Profil Lengkap <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>
@endif

<!-- Program Kerja Section -->
<section class="home-programs py-5">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">Apa yang kami lakukan</p><h2>Program kerja utama</h2></div>
            <p>Inisiatif yang dirancang dari kebutuhan nyata, dikerjakan bersama warga, dan diarahkan pada dampak berkelanjutan.</p>
        </div>
        <div class="program-poster-grid">
            @forelse(array_slice($workPrograms, 0, 3) as $program)
                @php($programThumbnail = $program['thumbnail_url'] ?? ($program['gallery'][0] ?? null))
                <a href="{{ route('program-kerja.detail', $program['id'] ?? '#') }}" class="program-poster">
                    <span class="program-tape" aria-hidden="true"></span>
                    <div class="program-poster-visual">
                        @if($programThumbnail)
                            <img src="{{ $programThumbnail }}" alt="{{ $program['title'] ?? 'Program kerja' }}" loading="lazy" decoding="async">
                        @else
                            <i class="fa-solid {{ $loop->iteration === 1 ? 'fa-seedling' : ($loop->iteration === 2 ? 'fa-people-group' : 'fa-lightbulb') }}"></i>
                        @endif
                        <span class="program-poster-number">0{{ $loop->iteration }}</span>
                    </div>
                    <div class="program-poster-copy">
                        <div class="program-poster-meta">
                            <span class="program-poster-status status-{{ $program['status'] ?? 'planned' }}">
                                {{ match($program['status'] ?? 'planned') { 'completed' => 'Selesai', 'ongoing' => 'Berjalan', default => 'Rencana' } }}
                            </span>
                            <span>{{ isset($program['start_date']) ? \Carbon\Carbon::parse($program['start_date'])->format('M Y') : 'Program KKN' }}</span>
                        </div>
                        <h3>{{ $program['title'] ?? 'Program Kerja' }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit($program['objective'] ?? $program['description'] ?? 'Inisiatif bersama warga desa.', 105) }}</p>
                        <div class="program-poster-foot">
                            <span>Lihat proses</span>
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </div>
                    </div>
                </a>
            @empty
                <div class="program-poster-empty">
                    <p class="text-center text-muted">Belum ada program kerja</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('program-kerja') }}" class="btn btn-outline-primary btn-lg">
                Lihat Semua Program Kerja
            </a>
        </div>
    </div>
</section>

<!-- Artikel Terbaru Section -->
<section class="home-stories py-5">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">Catatan lapangan</p><h2>Cerita terbaru</h2></div>
            <p>Kabar, proses, dan pembelajaran yang kami temukan selama menjalankan pengabdian di desa.</p>
        </div>
        <div class="story-edition-rule" aria-hidden="true"><span>Jurnal pengabdian</span><b>Vol. 01</b><span>Dari desa untuk semua</span></div>
        <div class="story-magazine">
            @forelse(array_slice($latestArticles, 0, 3) as $article)
                <article class="magazine-story">
                    <a href="{{ route('artikel.detail', $article['id'] ?? '#') }}" class="magazine-story-link">
                        <div class="magazine-story-media">
                        @if($article['thumbnail_url'] ?? false)
                                <img src="{{ $article['thumbnail_url'] }}" alt="{{ $article['title'] ?? 'Cerita KKN' }}" loading="lazy" decoding="async">
                        @else
                                <span class="magazine-placeholder"><i class="fa-solid fa-quote-left"></i></span>
                        @endif
                        </div>
                        <div class="magazine-story-copy">
                            <div class="magazine-story-meta">
                                <span>{{ $article['category'] ?? 'Catatan' }}</span>
                                <time>{{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d M Y') : 'Baru' }}</time>
                            </div>
                            <h3>{{ $article['title'] ?? 'Artikel' }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($article['content'] ?? 'Cerita dari perjalanan pengabdian kami.'), $loop->first ? 180 : 90) }}</p>
                            <div class="magazine-byline">
                                <span class="magazine-author-mark">{{ strtoupper(substr($article['author'] ?? 'K', 0, 1)) }}</span>
                                <span><b>{{ $article['author'] ?? 'Tim KKN' }}</b><small>Dari lapangan / {{ max(1, (int) ceil(str_word_count(strip_tags($article['content'] ?? '')) / 180)) }} menit baca</small></span>
                            </div>
                            <span class="magazine-read">Baca cerita <i class="fa-solid fa-arrow-right"></i></span>
                        </div>
                        <span class="magazine-index">0{{ $loop->iteration }}</span>
                    </a>
                </article>
            @empty
                <div class="story-magazine-empty">
                    <p class="text-center text-muted">Belum ada artikel</p>
                </div>
            @endforelse
        </div>
        <div class="story-running-line" aria-hidden="true">
            <span>WARGA / PROSES / DAMPAK / CERITA / WARGA / PROSES / DAMPAK / CERITA /</span>
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('artikel') }}" class="btn btn-outline-primary btn-lg">
                Lihat Semua Artikel
            </a>
        </div>
    </div>
</section>

<!-- Anggota Section -->
<section class="home-team py-5">
    <div class="container">
        <div class="section-heading">
            <div><p class="eyebrow">Orang di balik karya</p><h2>Temui tim kami</h2></div>
            <p>Beragam latar keilmuan, satu semangat untuk hadir dan bertumbuh bersama masyarakat.</p>
        </div>
        <div class="team-hanger-grid">
            @forelse(array_slice($members, 0, 6) as $member)
                <article class="team-hanger">
                    <div class="team-hanger-rig" aria-hidden="true">
                        <span class="team-cord team-cord-left"></span>
                        <span class="team-cord team-cord-right"></span>
                        <span class="team-ring"></span>
                        <span class="team-clip"></span>
                    </div>
                    <a href="{{ route('profil-kelompok.detail', $member['id']) }}" class="team-id-badge" aria-label="Lihat profil {{ $member['name'] ?? 'anggota' }}">
                        <span class="team-card-slot" aria-hidden="true"></span>
                        <header class="team-id-header">
                            <span>KKN<span>.</span>KARYA</span>
                            <small>#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</small>
                        </header>
                        <div class="team-id-photo">
                            @if($member['photo_url'] ?? false)
                                <img src="{{ $member['photo_url'] }}" alt="{{ $member['name'] ?? 'Anggota tim' }}" loading="lazy" decoding="async">
                            @else
                                <span><i class="fa-solid fa-user"></i></span>
                            @endif
                            <strong>{{ $member['position'] ?? 'Anggota' }}</strong>
                        </div>
                        <div class="team-id-copy">
                            <p>Nama anggota</p>
                            <h3>{{ $member['name'] ?? 'Anggota' }}</h3>
                            <div class="team-id-data">
                                <span><small>Program studi</small><b>{{ $member['prodi'] ?? '-' }}</b></span>
                                <span><small>NIM</small><b>{{ $member['nim'] ?? '-' }}</b></span>
                            </div>
                        </div>
                        <footer class="team-id-footer">
                            <span>Lihat profil lengkap</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </footer>
                    </a>
                </article>
            @empty
                <div class="team-hanger-empty">
                    <p class="text-center text-muted">Belum ada anggota terdaftar</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('profil-kelompok') }}" class="btn btn-outline-primary btn-lg">
                Lihat Semua Anggota
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white text-center">
    <div class="container">
        <h2 class="mb-4">Ingin Melihat Lebih Banyak?</h2>
        <p class="lead mb-4">Jelajahi galeri dokumentasi, timeline kegiatan, dan informasi lengkap lainnya di website kami.</p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="{{ route('galeri') }}" class="btn btn-light">
                <i class="fas fa-images"></i> Galeri
            </a>
            <a href="{{ route('timeline') }}" class="btn btn-light">
                <i class="fas fa-timeline"></i> Timeline
            </a>
            <a href="{{ route('kontak') }}" class="btn btn-light">
                <i class="fas fa-envelope"></i> Hubungi Kami
            </a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
document.querySelectorAll('[data-coverflow]').forEach((carousel) => {
    const cards = [...carousel.querySelectorAll('[data-coverflow-card]')];
    const dots = [...carousel.querySelectorAll('[data-coverflow-dot]')];
    if (!cards.length) return;

    let active = 0;
    let pointerStart = null;
    let autoplay = null;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const render = () => {
        cards.forEach((card, index) => {
            let offset = index - active;
            if (offset > cards.length / 2) offset -= cards.length;
            if (offset < -cards.length / 2) offset += cards.length;

            const distance = Math.abs(offset);
            const visible = distance <= 1;
            card.style.setProperty('--x', `${offset * 57}%`);
            card.style.setProperty('--z', `${distance * -230}px`);
            card.style.setProperty('--rotate', `${offset * -38}deg`);
            card.style.setProperty('--scale', Math.max(.76, 1 - (distance * .2)));
            card.style.setProperty('--layer', cards.length - distance);
            card.classList.toggle('is-active', offset === 0);
            card.classList.toggle('is-visible', visible);
            card.setAttribute('aria-hidden', offset === 0 ? 'false' : 'true');
        });
        dots.forEach((dot, index) => dot.classList.toggle('active', index === active));
    };

    const move = (direction) => {
        active = (active + direction + cards.length) % cards.length;
        render();
    };

    const stopAutoplay = () => {
        window.clearInterval(autoplay);
        autoplay = null;
    };

    const startAutoplay = () => {
        stopAutoplay();
        if (!reduceMotion && cards.length > 1 && !document.hidden) {
            autoplay = window.setInterval(() => move(1), 3800);
        }
    };

    const moveManually = (direction) => {
        move(direction);
        startAutoplay();
    };

    carousel.querySelector('[data-coverflow-prev]')?.addEventListener('click', () => moveManually(-1));
    carousel.querySelector('[data-coverflow-next]')?.addEventListener('click', () => moveManually(1));
    dots.forEach((dot) => dot.addEventListener('click', () => {
        active = Number(dot.dataset.coverflowDot);
        render();
        startAutoplay();
    }));

    carousel.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') moveManually(-1);
        if (event.key === 'ArrowRight') moveManually(1);
    });
    carousel.addEventListener('pointerdown', (event) => {
        stopAutoplay();
        pointerStart = event.clientX;
        carousel.setPointerCapture(event.pointerId);
    });
    carousel.addEventListener('pointerup', (event) => {
        if (pointerStart === null) return;
        const distance = event.clientX - pointerStart;
        if (Math.abs(distance) > 45) move(distance > 0 ? -1 : 1);
        pointerStart = null;
        startAutoplay();
    });
    carousel.addEventListener('mouseenter', stopAutoplay);
    carousel.addEventListener('mouseleave', startAutoplay);
    carousel.addEventListener('focusin', stopAutoplay);
    carousel.addEventListener('focusout', (event) => {
        if (!carousel.contains(event.relatedTarget)) startAutoplay();
    });
    document.addEventListener('visibilitychange', () => document.hidden ? stopAutoplay() : startAutoplay());

    render();
    startAutoplay();
});
</script>
@endsection
