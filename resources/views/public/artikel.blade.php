@extends('layouts.app')

@section('title', 'Artikel Kegiatan')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Artikel Kegiatan</h1>
        <p class="lead">Berita dan cerita kegiatan KKN kami</p>
    </div>
</section>

<!-- Search & Filter -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" class="form-control" id="search-artikel" placeholder="Cari artikel...">
            </div>
            <div class="col-md-6">
                <select class="form-select" id="filter-kategori">
                    <option value="">Semua Kategori</option>
                    <option value="edukasi">Edukasi</option>
                    <option value="sosialisasi">Sosialisasi</option>
                    <option value="gotong-royong">Gotong Royong</option>
                    <option value="dokumentasi">Dokumentasi</option>
                    <option value="acara">Acara Khusus</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Artikel List -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($articles as $article)
            <div class="col-lg-6 artikel-item" data-kategori="{{ $article['category'] ?? '' }}" data-title="{{ strtolower($article['title'] ?? '') }}">
                <div class="card h-100">
                    <div class="row g-0">
                        <div class="col-md-4">
                            @if($article['thumbnail_url'] ?? false)
                            <img src="{{ $article['thumbnail_url'] }}" class="img-fluid" alt="{{ $article['title'] }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="fas fa-newspaper text-muted" style="font-size: 2rem;"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">{{ $article['title'] ?? 'Artikel' }}</h5>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-calendar"></i>
                                    {{ isset($article['published_at']) ? \Carbon\Carbon::parse($article['published_at'])->format('d M Y') : '-' }}
                                </p>
                                <p class="card-text text-muted small mb-2">
                                    <i class="fas fa-user"></i>
                                    {{ $article['author'] ?? 'Penulis tidak diketahui' }}
                                </p>
                                <span class="badge bg-secondary">{{ $article['category'] ?? 'Umum' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('artikel.detail', $article['id'] ?? '#') }}" class="btn btn-sm btn-primary">
                            Baca Selengkapnya <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p>Belum ada artikel terdaftar</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Back to Home -->
<section class="py-4 bg-light text-center">
    <div class="container">
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</section>
@endsection

@section('scripts')
<script>
const searchInput = document.getElementById('search-artikel');
const filterKategori = document.getElementById('filter-kategori');

function filterArtikel() {
    const searchText = searchInput.value.toLowerCase();
    const kategori = filterKategori.value;

    document.querySelectorAll('.artikel-item').forEach(item => {
        const title = item.getAttribute('data-title');
        const itemKategori = item.getAttribute('data-kategori');
        
        const matchSearch = title.includes(searchText);
        const matchKategori = kategori === '' || itemKategori === kategori;
        
        if (matchSearch && matchKategori) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

searchInput.addEventListener('keyup', filterArtikel);
filterKategori.addEventListener('change', filterArtikel);
</script>
@endsection
