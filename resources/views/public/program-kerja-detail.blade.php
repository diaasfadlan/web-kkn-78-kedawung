@extends('layouts.app')

@section('title', $program['title'] ?? 'Detail Program Kerja')

@section('content')
@php($programThumbnail = $program['thumbnail_url'] ?? ($program['gallery'][0] ?? null))
@php($programGallery = isset($program['thumbnail_url']) && $program['thumbnail_url'] ? ($program['gallery'] ?? []) : array_slice($program['gallery'] ?? [], 1))
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>{{ $program['title'] ?? 'Program Kerja' }}</h1>
        <p class="lead">Detail dan dokumentasi program kerja</p>
    </div>
</section>

<!-- Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if($programThumbnail)
                <img src="{{ $programThumbnail }}" alt="{{ $program['title'] }}" class="img-fluid rounded-3 mb-4 shadow" decoding="async">
                @endif

                <div class="mb-4">
                    <span class="badge bg-{{ $program['status'] == 'completed' ? 'success' : ($program['status'] == 'ongoing' ? 'warning' : 'info') }}">
                        {{ ucfirst($program['status'] ?? 'planned') }}
                    </span>
                </div>

                <h2 class="mb-4">{{ $program['title'] ?? 'Program Kerja' }}</h2>

                <div class="mb-5">
                    <h4>Deskripsi Program</h4>
                    <p class="text-justify">{{ $program['description'] ?? 'Deskripsi tidak tersedia' }}</p>
                </div>

                <div class="mb-5">
                    <h4>Tujuan Program</h4>
                    <p class="text-justify">{{ $program['objective'] ?? 'Tujuan tidak tersedia' }}</p>
                </div>

                <div class="mb-5">
                    <h4>Rincian Waktu</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <strong>Tanggal Mulai:</strong><br>
                                {{ isset($program['start_date']) ? \Carbon\Carbon::parse($program['start_date'])->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <strong>Tanggal Selesai:</strong><br>
                                {{ isset($program['end_date']) ? \Carbon\Carbon::parse($program['end_date'])->format('d F Y') : '-' }}
                            </p>
                        </div>
                    </div>
                </div>

                @if(count($programGallery) > 0)
                <div class="mb-5">
                    <h4>Dokumentasi Kegiatan</h4>
                    <div class="row g-3">
                        @foreach($programGallery as $image)
                        <div class="col-md-6">
                            <img src="{{ $image }}" alt="Dokumentasi" class="img-fluid rounded" style="height: 250px; object-fit: cover;" loading="lazy" decoding="async">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informasi Program</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $program['status'] == 'completed' ? 'success' : ($program['status'] == 'ongoing' ? 'warning' : 'info') }} fs-6">
                                {{ ucfirst($program['status'] ?? 'planned') }}
                            </span>
                        </p>
                        <p>
                            <strong>Durasi:</strong><br>
                            @if(isset($program['start_date']) && isset($program['end_date']))
                                {{ \Carbon\Carbon::parse($program['start_date'])->diffInDays(\Carbon\Carbon::parse($program['end_date'])) }} hari
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Navigasi</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('program-kerja') }}" class="btn btn-sm btn-outline-primary w-100 mb-2">
                            <i class="fas fa-arrow-left"></i> Kembali ke Program Kerja
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="fas fa-home"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
