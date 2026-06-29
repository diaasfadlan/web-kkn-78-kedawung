@extends('layouts.app')

@section('title', 'Program Kerja')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Program Kerja KKN</h1>
        <p class="lead">Daftar program kerja yang telah kami rencanakan dan laksanakan</p>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary filter-btn active" data-filter="all">Semua</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="planned">Direncanakan</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="ongoing">Sedang Berjalan</button>
                    <button type="button" class="btn btn-outline-primary filter-btn" data-filter="completed">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Program List -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @forelse($programs as $program)
            @php($programThumbnail = $program['thumbnail_url'] ?? ($program['gallery'][0] ?? null))
            <div class="col-md-6 col-lg-4 program-card" data-status="{{ $program['status'] ?? 'planned' }}">
                <div class="card h-100">
                    @if($programThumbnail)
                    <img src="{{ $programThumbnail }}" class="card-img-top" alt="{{ $program['title'] }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-briefcase text-muted" style="font-size: 3rem;"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $program['title'] ?? 'Program Kerja' }}</h5>
                        <p class="card-text text-muted text-truncate" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                            {{ $program['description'] ?? '-' }}
                        </p>
                        <p class="text-sm mb-2">
                            <i class="fas fa-calendar text-primary"></i>
                            {{ isset($program['start_date']) ? \Carbon\Carbon::parse($program['start_date'])->format('d M Y') : '-' }}
                        </p>
                        <div>
                            <span class="badge bg-{{ $program['status'] == 'completed' ? 'success' : ($program['status'] == 'ongoing' ? 'warning' : 'info') }}">
                                {{ ucfirst($program['status'] ?? 'planned') }}
                            </span>
                        </div>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="{{ route('program-kerja.detail', $program['id'] ?? '#') }}" class="btn btn-sm btn-primary w-100">
                            Lihat Detail <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <p>Belum ada program kerja terdaftar</p>
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
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.program-card').forEach(card => {
            if (filter === 'all' || card.getAttribute('data-status') === filter) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
