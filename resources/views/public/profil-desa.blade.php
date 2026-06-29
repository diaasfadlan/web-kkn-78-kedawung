@extends('layouts.app')

@section('title', 'Profil Desa')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Profil Desa</h1>
        <p class="lead">Mengenal lebih dekat desa tempat kami melaksanakan KKN</p>
    </div>
</section>

<!-- Desa Info -->
<section class="py-5">
    <div class="container">
        @if($village)
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4">{{ $village['name'] ?? 'Nama Desa' }}</h2>
                
                <div class="mb-5">
                    <h4>Informasi Umum</h4>
                    <table class="table">
                        <tr>
                            <td width="30%"><strong>Alamat</strong></td>
                            <td>{{ $village['address'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kecamatan</strong></td>
                            <td>{{ $village['district'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kabupaten/Kota</strong></td>
                            <td>{{ $village['regency'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Provinsi</strong></td>
                            <td>{{ $village['province'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Kode Pos</strong></td>
                            <td>{{ $village['postal_code'] ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                <div class="mb-5">
                    <h4>Sejarah Desa</h4>
                    <p class="text-justify">{{ $village['history'] ?? 'Informasi sejarah desa tidak tersedia' }}</p>
                </div>

                <div class="mb-5">
                    <h4>Filosofi Desa</h4>
                    <p class="text-justify">{{ $village['philosophy'] ?? 'Informasi filosofi desa tidak tersedia' }}</p>
                </div>

                <div class="mb-5">
                    <h4>Demografi Masyarakat</h4>
                    <p class="text-justify">{{ $village['demographics'] ?? 'Informasi demografi tidak tersedia' }}</p>
                </div>

                <div class="mb-5">
                    <h4>Potensi Desa</h4>
                    <p class="text-justify">{{ $village['potential'] ?? 'Informasi potensi desa tidak tersedia' }}</p>
                </div>
            </div>

            <div class="col-lg-4">
                @if($village['map_url'] ?? false)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Lokasi Desa</h5>
                    </div>
                    <div class="card-body p-0">
                        <iframe width="100%" height="300" src="{{ $village['map_url'] }}" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
                @endif

                @if($village['image_url'] ?? false)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Gambar Desa</h5>
                    </div>
                    <div class="card-body p-0">
                        <img src="{{ $village['image_url'] }}" alt="Desa" class="img-fluid" loading="lazy" decoding="async">
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Kontak Pemerintah Desa</h5>
                    </div>
                    <div class="card-body">
                        @if($village['contact_phone'] ?? false)
                        <p class="mb-2">
                            <i class="fas fa-phone text-primary"></i>
                            <strong>Telepon:</strong><br>
                            {{ $village['contact_phone'] }}
                        </p>
                        @endif
                        @if($village['contact_email'] ?? false)
                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary"></i>
                            <strong>Email:</strong><br>
                            {{ $village['contact_email'] }}
                        </p>
                        @endif
                        @if($village['contact_address'] ?? false)
                        <p>
                            <i class="fas fa-map-marker-alt text-primary"></i>
                            <strong>Alamat Kantor:</strong><br>
                            {{ $village['contact_address'] }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <!-- UMKM Section -->
        @if($village['umkm'] ?? false)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">UMKM Desa</h3>
            </div>
            @foreach($village['umkm'] as $umkm)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    @if($umkm['image'] ?? false)
                    <img src="{{ $umkm['image'] }}" class="card-img-top" alt="{{ $umkm['name'] }}" style="height: 200px; object-fit: cover;" loading="lazy" decoding="async">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $umkm['name'] ?? 'UMKM' }}</h5>
                        <p class="card-text text-muted">{{ $umkm['description'] ?? '-' }}</p>
                        <p class="text-sm">
                            <strong>Pemilik:</strong> {{ $umkm['owner'] ?? '-' }}<br>
                            <strong>Kontak:</strong> {{ $umkm['contact'] ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @else
        <div class="alert alert-info text-center">
            <p>Informasi profil desa belum tersedia</p>
        </div>
        @endif
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
