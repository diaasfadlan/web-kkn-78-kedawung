@extends('layouts.app')

@section('title', 'Profil Kelompok')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Profil Kelompok KKN</h1>
        <p class="lead">Berkenalan dengan anggota dan struktur kelompok kami</p>
    </div>
</section>

<!-- Kelompok Info -->
<section class="py-5 bg-light">
    <div class="container">
        @if($group)
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                @if($group['photo_url'] ?? false)
                <img src="{{ $group['photo_url'] }}" alt="Foto Kelompok" class="img-fluid rounded-3 shadow" decoding="async">
                @endif
            </div>
            <div class="col-md-6 ps-md-5">
                <h2 class="mb-4">{{ $group['name'] ?? 'Kelompok KKN' }}</h2>
                <p class="mb-3">
                    <i class="fas fa-map-marker-alt text-danger"></i>
                    <strong>Lokasi:</strong> {{ $group['location'] ?? '-' }}
                </p>
                <p class="mb-3">
                    <i class="fas fa-calendar text-primary"></i>
                    <strong>Periode:</strong> {{ $group['period'] ?? '-' }}
                </p>
                <p class="mb-4">
                    <i class="fas fa-university text-info"></i>
                    <strong>Universitas:</strong> {{ $group['university'] ?? '-' }}
                </p>
                <p class="text-justify">{{ $group['description'] ?? 'Deskripsi kelompok tidak tersedia' }}</p>
            </div>
        </div>

        <!-- Dosen Pembimbing -->
        @if($lecturer)
        <div class="row my-5">
            <div class="col-12">
                <h3 class="mb-4">Dosen Pembimbing Lapangan</h3>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card text-center h-100">
                    @if($lecturer['photo_url'] ?? false)
                    <img src="{{ $lecturer['photo_url'] }}" class="card-img-top" alt="{{ $lecturer['name'] }}" style="height: 250px; object-fit: cover;" loading="lazy" decoding="async">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-user text-muted" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $lecturer['name'] ?? 'Dosen' }}</h5>
                        <p class="text-muted">{{ $lecturer['nidn'] ?? '-' }}</p>
                        <p class="text-muted">{{ $lecturer['department'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>
</section>

<!-- Struktur Organisasi -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Struktur Organisasi Kelompok</h2>
        <div class="row g-4 justify-content-center">
            <!-- Ketua -->
            <div class="col-md-6 col-lg-3 text-center">
                <div class="card border-primary h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">Ketua Kelompok</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="fw-bold">-</p>
                    </div>
                </div>
            </div>

            <!-- Vice Ketua -->
            <div class="col-md-6 col-lg-3 text-center">
                <div class="card border-secondary h-100">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">Wakil Ketua</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="fw-bold">-</p>
                    </div>
                </div>
            </div>

            <!-- Sekretaris -->
            <div class="col-md-6 col-lg-3 text-center">
                <div class="card border-info h-100">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">Sekretaris</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="fw-bold">-</p>
                    </div>
                </div>
            </div>

            <!-- Bendahara -->
            <div class="col-md-6 col-lg-3 text-center">
                <div class="card border-success h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">Bendahara</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3" style="height: 150px; background: #f0f0f0; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <p class="fw-bold">-</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Daftar Anggota -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Daftar Anggota Kelompok</h2>
        <div class="row g-4">
            @forelse($members as $member)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 text-center member-preview-card">
                    <a href="{{ route('profil-kelompok.detail', $member['id']) }}" class="member-card-main" aria-label="Lihat profil {{ $member['name'] ?? 'anggota' }}">
                    @if($member['photo_url'] ?? false)
                    <img src="{{ $member['photo_url'] }}" class="card-img-top" alt="{{ $member['name'] }}" style="height: 250px; object-fit: cover;" loading="lazy" decoding="async">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                        <i class="fas fa-user text-muted" style="font-size: 4rem;"></i>
                    </div>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $member['name'] ?? 'Anggota' }}</h5>
                        <p class="text-muted mb-1">
                            <small><strong>NIM:</strong> {{ $member['nim'] ?? '-' }}</small>
                        </p>
                        <p class="text-muted mb-2">
                            <small>{{ $member['prodi'] ?? '-' }}</small>
                        </p>
                        <span class="badge bg-primary">{{ $member['position'] ?? 'Anggota' }}</span>
                        <span class="member-detail-cue">Lihat ID Card <i class="fa-solid fa-arrow-right"></i></span>
                    </div>
                    </a>
                    @if($member['social_media'] ?? false)
                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-center gap-2">
                            @if($member['social_media']['instagram'] ?? false)
                            <a href="{{ $member['social_media']['instagram'] }}" target="_blank" class="text-muted">
                                <i class="fab fa-instagram"></i>
                            </a>
                            @endif
                            @if($member['social_media']['whatsapp'] ?? false)
                            <a href="{{ $member['social_media']['whatsapp'] }}" target="_blank" class="text-muted">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            @endif
                            @if($member['social_media']['email'] ?? false)
                            <a href="mailto:{{ $member['social_media']['email'] }}" class="text-muted">
                                <i class="fas fa-envelope"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="col-12">
                <p class="text-center text-muted">Belum ada anggota terdaftar</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Back to Home -->
<section class="py-4 bg-white text-center">
    <div class="container">
        <a href="{{ route('home') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</section>
@endsection
