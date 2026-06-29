@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Dashboard Admin</h1>
        <p class="lead">Kelola konten website KKN</p>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-file-alt text-primary" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Total Artikel</h5>
                        <h2 class="text-primary">{{ $stats['total_articles'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-briefcase text-success" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Program Kerja</h5>
                        <h2 class="text-success">{{ $stats['total_programs'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-images text-info" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Galeri</h5>
                        <h2 class="text-info">{{ $stats['total_galleries'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-users text-warning" style="font-size: 2.5rem;"></i>
                        <h5 class="card-title mt-3">Anggota</h5>
                        <h2 class="text-warning">{{ $stats['total_members'] ?? 0 }}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Editorial workspace -->
<section class="admin-workspace py-5">
    <div class="container">
        <div class="admin-section-heading">
            <div>
                <span class="admin-kicker">Ruang kerja</span>
                <h2>Yang perlu diperhatikan.</h2>
            </div>
            <span class="admin-heading-note">KKN / {{ date('Y') }}</span>
        </div>

        <div class="row g-4 align-items-stretch">
            <div class="col-lg-5">
                @php($featuredProgram = $recentPrograms[0] ?? null)
                <article class="admin-feature-program h-100">
                    <div class="program-stamp"><i class="fa-solid fa-bolt"></i> Fokus utama</div>
                    <div class="program-orbit" aria-hidden="true"><span>01</span></div>

                    <div class="program-feature-content">
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <span class="program-label">Program Utama</span>
                            @if($featuredProgram)
                                <span class="program-status program-status-{{ $featuredProgram['status'] ?? 'planned' }}">
                                    {{ match($featuredProgram['status'] ?? 'planned') { 'completed' => 'Selesai', 'ongoing' => 'Berjalan', default => 'Rencana' } }}
                                </span>
                            @endif
                        </div>

                        @if($featuredProgram)
                            <h3>{{ $featuredProgram['title'] ?? 'Program kerja' }}</h3>
                            <p>{{ $featuredProgram['objective'] ?? $featuredProgram['description'] ?? 'Program kerja terbaru kelompok KKN.' }}</p>
                            <div class="program-date">
                                <i class="fa-regular fa-calendar"></i>
                                <span>
                                    {{ isset($featuredProgram['start_date']) ? \Carbon\Carbon::parse($featuredProgram['start_date'])->format('d M') : 'Tanggal belum diatur' }}
                                    @if(isset($featuredProgram['end_date'])) - {{ \Carbon\Carbon::parse($featuredProgram['end_date'])->format('d M Y') }} @endif
                                </span>
                            </div>
                            <div class="program-actions">
                                @if(isset($featuredProgram['id']))
                                    <a href="{{ route('program.edit', $featuredProgram['id']) }}" class="btn btn-dark">Buka program <i class="fa-solid fa-arrow-right"></i></a>
                                @endif
                                <a href="{{ route('program.index') }}" class="program-text-link">Lihat semua</a>
                            </div>
                        @else
                            <h3>Mulai program pertama.</h3>
                            <p>Susun kegiatan utama agar tim punya arah kerja yang jelas.</p>
                            <a href="{{ route('program.create') }}" class="btn btn-dark">Buat program <i class="fa-solid fa-plus"></i></a>
                        @endif
                    </div>
                </article>
            </div>

            <div class="col-lg-7">
                <section class="admin-story-board h-100" aria-labelledby="recent-story-title">
                    <header class="story-board-header">
                        <div>
                            <span class="story-issue">Edisi terbaru</span>
                            <h3 id="recent-story-title">Cerita Baru</h3>
                        </div>
                        <a href="{{ route('artikel.create') }}" class="story-add" aria-label="Tulis artikel baru"><i class="fa-solid fa-plus"></i></a>
                    </header>

                    <div class="story-list">
                        @forelse($recentArticles as $article)
                            <article class="story-row">
                                <div class="story-thumb">
                                    @if($article['thumbnail_url'] ?? false)
                                        <img src="{{ $article['thumbnail_url'] }}" alt="" loading="lazy">
                                    @else
                                        <span>{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    @endif
                                </div>
                                <div class="story-copy">
                                    <div class="story-meta">
                                        <span>{{ $article['category'] ?? 'Umum' }}</span>
                                        <time>{{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d M Y') : 'Belum terbit' }}</time>
                                    </div>
                                    <h4>{{ $article['title'] ?? 'Artikel tanpa judul' }}</h4>
                                    <small>Oleh {{ $article['author'] ?? 'Tim KKN' }}</small>
                                </div>
                                @if(isset($article['id']))
                                    <div class="story-actions">
                                        <a href="{{ route('artikel.edit', $article['id']) }}" aria-label="Edit {{ $article['title'] ?? 'artikel' }}"><i class="fa-solid fa-pen"></i></a>
                                        <form action="{{ route('artikel.destroy', $article['id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" aria-label="Hapus {{ $article['title'] ?? 'artikel' }}" onclick="return confirm('Hapus artikel ini?')"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="story-empty">
                                <span>Belum ada cerita.</span>
                                <a href="{{ route('artikel.create') }}">Tulis cerita pertama <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                        @endforelse
                    </div>

                    <a href="{{ route('artikel.index') }}" class="story-board-footer">Buka seluruh arsip <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                </section>
            </div>
        </div>

        <nav class="admin-quick-strip" aria-label="Menu manajemen cepat">
            <span class="quick-strip-label">Pindah cepat</span>
            <a href="{{ route('gallery.index') }}"><i class="fa-solid fa-images"></i> Galeri</a>
            <a href="{{ route('timeline.index') }}"><i class="fa-solid fa-timeline"></i> Timeline</a>
            <a href="{{ route('group.index') }}"><i class="fa-solid fa-users"></i> Anggota</a>
            <a href="{{ route('village.edit', 'main') }}"><i class="fa-solid fa-map-location-dot"></i> Profil Desa</a>
            <a href="{{ route('contact.edit', 'main') }}"><i class="fa-solid fa-address-book"></i> Kontak</a>
        </nav>
    </div>
</section>
@endsection
