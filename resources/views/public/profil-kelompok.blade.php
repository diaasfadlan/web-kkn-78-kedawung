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
        <div class="row g-4 justify-content-center page-card-rail organization-card-rail">
            @foreach($organization as $position => $positionMembers)
            <div class="col-md-6 col-lg-3 text-center page-card-item">
                <div class="card organization-card h-100">
                    <div class="card-header organization-card-header">
                        <h6 class="mb-0">{{ $position }}</h6>
                    </div>
                    <div class="card-body organization-card-body">
                        @forelse($positionMembers as $organizationMember)
                            <a href="{{ route('profil-kelompok.detail', $organizationMember['id']) }}" class="organization-member">
                                <div class="organization-photo">
                                    @if($organizationMember['photo_url'] ?? false)
                                        <img src="{{ $organizationMember['photo_url'] }}" alt="Foto {{ $organizationMember['name'] ?? 'anggota' }}" loading="lazy" decoding="async">
                                    @else
                                        <i class="fas fa-user text-muted"></i>
                                    @endif
                                </div>
                                <p class="fw-bold mb-0">{{ $organizationMember['name'] ?? 'Anggota' }}</p>
                            </a>
                        @empty
                            <div class="organization-photo organization-photo-empty">
                                <i class="fas fa-user text-muted"></i>
                            </div>
                            <p class="fw-bold mb-0">-</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Daftar Anggota -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Daftar Anggota Kelompok</h2>
        <div class="row g-4 page-card-rail member-card-rail">
            @forelse($members as $member)
            <div class="col-md-6 col-lg-4 page-card-item">
                <div class="card h-100 text-center member-preview-card">
                    <a href="{{ route('profil-kelompok.detail', $member['id']) }}" class="member-card-main" aria-label="Lihat profil {{ $member['name'] ?? 'anggota' }}">
                    @if($member['photo_url'] ?? false)
                    <img src="{{ $member['photo_url'] }}" class="card-img-top member-list-photo" alt="{{ $member['name'] }}" loading="lazy" decoding="async">
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
                            <a href="@waUrl($member['social_media']['whatsapp'])" target="_blank" rel="noopener" class="text-muted">
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
