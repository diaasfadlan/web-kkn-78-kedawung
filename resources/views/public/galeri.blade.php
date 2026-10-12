@extends('layouts.app')

@section('title', 'Galeri Dokumentasi')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Galeri Dokumentasi</h1>
        <p class="lead">Koleksi foto dan video kegiatan KKN kami</p>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="d-flex gap-2 justify-content-md-center page-filter-rail" aria-label="Filter galeri">
            <button class="btn btn-outline-primary filter-btn active" data-filter="all">Semua</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="edukasi">Edukasi</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="sosialisasi">Sosialisasi</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="gotong-royong">Gotong Royong</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="dokumentasi">Dokumentasi Desa</button>
            <button class="btn btn-outline-primary filter-btn" data-filter="penutupan">Acara Penutupan</button>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="py-5">
    <div class="container">
        <div class="row g-3 page-card-rail gallery-card-rail" id="gallery-grid">
            @forelse($galleries as $gallery)
            <div class="col-md-6 col-lg-4 gallery-item page-card-item" data-category="{{ $gallery['category'] ?? 'dokumentasi' }}">
                <button type="button" class="position-relative overflow-hidden rounded-3 gallery-card-media gallery-card-button" data-bs-toggle="modal" data-bs-target="#galleryModal" data-title="{{ $gallery['title'] ?? 'Dokumentasi' }}" data-img="{{ $gallery['image_url'] ?? '' }}" data-desc="{{ $gallery['description'] ?? '-' }}" aria-label="Buka galeri {{ $gallery['title'] ?? 'Dokumentasi' }}">
                    @if($gallery['image_url'] ?? false)
                    <img src="{{ $gallery['image_url'] }}" alt="{{ $gallery['title'] }}" class="img-fluid w-100 h-100" style="object-fit: cover;" loading="lazy" decoding="async">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center w-100 h-100">
                        <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                    </div>
                    @endif
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center" style="opacity: 0; transition: opacity 0.3s ease;">
                        <i class="fas fa-search text-white" style="font-size: 2rem;"></i>
                    </div>
                    <span class="position-absolute top-0 end-0 m-2 badge bg-primary">{{ $gallery['category'] ?? 'Dokumentasi' }}</span>
                </button>
                <h6 class="mt-3 mb-1">{{ $gallery['title'] ?? 'Foto' }}</h6>
                <p class="text-muted small">{{ $gallery['description'] ?? '-' }}</p>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p>Belum ada galeri terdaftar</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Single Dynamic Modal for gallery items -->
<div class="modal fade gallery-modal" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalTitle">Dokumentasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="" class="gallery-modal-image img-fluid rounded" id="galleryModalImg" loading="lazy" decoding="async" style="display: none; max-height: 70vh; object-fit: contain;">
                <p class="mt-3 text-muted" id="galleryModalDesc">-</p>
            </div>
        </div>
    </div>
</div>

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
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');

        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        document.querySelectorAll('.gallery-item').forEach(item => {
            if (filter === 'all' || item.getAttribute('data-category') === filter) {
                item.style.display = 'block';
                item.classList.add('animate__animated', 'animate__fadeIn');
            } else {
                item.style.display = 'none';
            }
        });
    });
});

const galleryModal = document.getElementById('galleryModal');
if (galleryModal) {
    galleryModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        if (!button) return;
        const title = button.getAttribute('data-title') || 'Dokumentasi';
        const img = button.getAttribute('data-img') || '';
        const desc = button.getAttribute('data-desc') || '-';

        document.getElementById('galleryModalTitle').textContent = title;
        const imgEl = document.getElementById('galleryModalImg');
        if (img) {
            imgEl.src = img;
            imgEl.alt = title;
            imgEl.style.display = 'inline-block';
        } else {
            imgEl.src = '';
            imgEl.style.display = 'none';
        }
        document.getElementById('galleryModalDesc').textContent = desc;
    });
}
</script>
@endsection
