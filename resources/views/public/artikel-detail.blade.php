@extends('layouts.app')

@section('title', $article['title'] ?? 'Detail Artikel')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>{{ $article['title'] ?? 'Artikel' }}</h1>
        <p class="lead">
            <i class="fas fa-calendar"></i>
            {{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d F Y') : '-' }}
        </p>
    </div>
</section>

<!-- Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if($article['thumbnail_url'] ?? false)
                <img src="{{ $article['thumbnail_url'] }}" alt="{{ $article['title'] }}" class="img-fluid rounded-3 mb-4 shadow" decoding="async">
                @endif

                <div class="mb-4">
                    <span class="badge bg-primary">{{ $article['category'] ?? 'Umum' }}</span>
                    <span class="badge bg-secondary ms-2">
                        <i class="fas fa-user"></i> {{ $article['author'] ?? 'Penulis' }}
                    </span>
                </div>

                <div class="article-content">
                    <p class="text-justify">{{ $article['content'] ?? 'Konten tidak tersedia' }}</p>
                </div>

                @if($article['gallery'] && count($article['gallery']) > 0)
                <hr class="my-5">
                <h4>Galeri Kegiatan</h4>
                <div class="row g-3 mb-5">
                    @foreach($article['gallery'] as $image)
                    <div class="col-md-6">
                        <img src="{{ $image }}" alt="Dokumentasi" class="img-fluid rounded" style="height: 250px; object-fit: cover;" loading="lazy" decoding="async">
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-4 sticky-top" style="top: 100px;">
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
                        <div class="d-flex gap-2">
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
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="mb-4">Artikel Terkait</h3>
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <p class="text-muted small">Artikel terkait akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
