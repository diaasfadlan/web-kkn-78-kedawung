@extends('layouts.app')

@section('title', $article['title'] ?? 'Detail Artikel')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($article['content'] ?? ''), 150))
@if(!empty($article['thumbnail_url']))
    @section('og_image', $article['thumbnail_url'])
@endif

@section('content')
<!-- Hero Section -->
<section class="hero story-detail-hero">
    <div class="container">
        <h1>{{ $article['title'] ?? 'Artikel' }}</h1>
        <p class="lead">
            <i class="fas fa-calendar"></i>
            {{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d F Y') : '-' }}
        </p>
    </div>
</section>

<!-- Content -->
<section class="py-5 story-detail-page">
    <div class="container">
        <div class="row g-4 g-xl-5">
            <div class="col-lg-8 story-detail-main">
                @if($article['thumbnail_url'] ?? false)
                <img src="{{ $article['thumbnail_url'] }}" alt="{{ $article['title'] }}" class="story-detail-cover rounded-3 mb-4 shadow" decoding="async">
                @endif

                <div class="story-detail-meta mb-4">
                    <span class="badge bg-primary">{{ $article['category'] ?? 'Umum' }}</span>
                    <span class="badge bg-secondary ms-2">
                        <i class="fas fa-user"></i> {{ $article['author'] ?? 'Penulis' }}
                    </span>
                </div>

                <div class="article-content" style="line-height: 1.8; font-size: 1.05rem;">
                    <p class="text-justify">{!! nl2br(e($article['content'] ?? 'Konten tidak tersedia')) !!}</p>
                </div>

                @if(!empty($article['gallery']))
                <hr class="my-5">
                <h4>Galeri Kegiatan</h4>
                <div class="row g-3 mb-5">
                    @foreach($article['gallery'] as $image)
                    <div class="col-md-6">
                        <img src="{{ $image }}" alt="Dokumentasi" class="story-gallery-image rounded" loading="lazy" decoding="async">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="col-lg-4 story-detail-sidebar">
                <div class="card mb-4 story-info-card sticky-lg-top">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Artikel</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Kategori:</strong><br>
                            <span class="badge bg-primary">{{ $article['category'] ?? 'Umum' }}</span>
                        </p>
                        <p class="mb-3">
                            <strong>Penulis:</strong><br>
                            {{ $article['author'] ?? 'Penulis tidak diketahui' }}
                        </p>
                        <p>
                            <strong>Tanggal Publikasi:</strong><br>
                            {{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d F Y H:i') : '-' }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Navigasi</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('artikel') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke Artikel
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-home"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Bagikan Artikel</h5>
                    </div>
                    <div class="card-body">
                        <div class="story-share-actions d-flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ $article['title'] ?? 'Artikel' }}" target="_blank" class="btn btn-sm btn-outline-info">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://api.whatsapp.com/send?text={{ $article['title'] ?? 'Artikel' }} {{ url()->current() }}" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('{{ url()->current() }}')">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
@if(!empty($relatedArticles) && count($relatedArticles) > 0)
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-heading mb-4">
            <div>
                <p class="eyebrow">Rekomendasi bacaan</p>
                <h3>Artikel Terkait Lainnya</h3>
            </div>
        </div>
        <div class="row g-4">
            @foreach($relatedArticles as $related)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 story-list-card shadow-sm border-0">
                    @if(!empty($related['thumbnail_url']))
                        <img src="{{ $related['thumbnail_url'] }}" alt="{{ $related['title'] ?? 'Artikel' }}" class="card-img-top" style="height: 190px; object-fit: cover;" loading="lazy">
                    @else
                        <div class="bg-secondary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 190px;">
                            <i class="fas fa-newspaper fa-2x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $related['category'] ?? 'Umum' }}</span>
                            <small class="text-muted ms-2">{{ isset($related['published_at']) ? \Carbon\Carbon::parse($related['published_at'])->format('d M Y') : '' }}</small>
                        </div>
                        <h5 class="card-title fw-bold">
                            <a href="{{ route('artikel.detail', $related['id'] ?? '') }}" class="text-decoration-none text-dark">
                                {{ \Illuminate\Support\Str::limit($related['title'] ?? 'Artikel', 60) }}
                            </a>
                        </h5>
                        <p class="card-text text-muted small mt-auto">
                            {{ \Illuminate\Support\Str::limit(strip_tags($related['content'] ?? ''), 90) }}
                        </p>
                        <a href="{{ route('artikel.detail', $related['id'] ?? '') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                            Baca Artikel <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
