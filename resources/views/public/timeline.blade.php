@extends('layouts.app')

@section('title', 'Timeline Kegiatan')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Timeline Kegiatan</h1>
        <p class="lead">Alur kegiatan KKN dari awal hingga akhir</p>
    </div>
</section>

<!-- Timeline -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="timeline">
                    @forelse($timelines as $timeline)
                    <div class="timeline-item {{ $loop->odd ? 'timeline-left' : 'timeline-right' }}">
                        <div class="timeline-marker">
                            <div class="marker" style="background-color: {{ $timeline['color'] ?? '#3498db' }};">
                                <i class="fas {{ $timeline['icon'] ?? 'fa-circle' }}"></i>
                            </div>
                        </div>
                        <div class="timeline-content">
                            <div class="card">
                                <div class="card-header" style="background-color: {{ $timeline['color'] ?? '#3498db' }}20; border-left: 4px solid {{ $timeline['color'] ?? '#3498db' }};">
                                    <h5 class="mb-0">{{ $timeline['title'] ?? 'Kegiatan' }}</h5>
                                    <small class="text-muted">
                                        {{ isset($timeline['date']) ? \Carbon\Carbon::parse($timeline['date'])->format('d F Y') : '-' }}
                                    </small>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $timeline['description'] ?? 'Deskripsi tidak tersedia' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info text-center">
                        <p>Belum ada timeline terdaftar</p>
                    </div>
                    @endforelse
                </div>
            </div>
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

@section('styles')
<style>
.timeline {
    position: relative;
    padding: 2rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 100%;
    background: #e0e0e0;
}

.timeline-item {
    margin-bottom: 3rem;
    position: relative;
}

.timeline-left {
    margin-left: 0;
    text-align: right;
}

.timeline-right {
    margin-left: auto;
    text-align: left;
}

.timeline-marker {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    top: 0;
    z-index: 10;
}

.marker {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #3498db;
    border: 4px solid white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.timeline-left .timeline-content {
    margin-right: 5%;
    width: 45%;
}

.timeline-right .timeline-content {
    margin-left: 5%;
    width: 45%;
}

@media (max-width: 768px) {
    .timeline::before {
        left: 0;
        transform: translateX(-2px);
    }

    .timeline-item {
        margin-left: 0;
        text-align: left;
    }

    .timeline-left,
    .timeline-right {
        margin-left: 0;
        text-align: left;
    }

    .timeline-marker {
        left: 0;
        transform: none;
    }

    .timeline-left .timeline-content,
    .timeline-right .timeline-content {
        margin-left: 70px;
        margin-right: 0;
        width: calc(100% - 70px);
    }
}
</style>
@endsection
