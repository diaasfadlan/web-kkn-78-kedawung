@extends('layouts.app')

@section('title', 'Kontak')

@section('content')
<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Hubungi Kami</h1>
        <p class="lead">Kontak informasi dan lokasi kelompok KKN</p>
    </div>
</section>

<!-- Contact Info -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-lg-6">
                <h2 class="mb-4">Informasi Kontak</h2>
                
                @if($contact)
                <div class="mb-4">
                    <h5><i class="fas fa-map-marker-alt text-primary"></i> Alamat</h5>
                    <p>{{ $contact['address'] ?? 'Alamat tidak tersedia' }}</p>
                </div>

                @if($contact['phone'] ?? false)
                <div class="mb-4">
                    <h5><i class="fas fa-phone text-primary"></i> Telepon Ketua</h5>
                    <p>
                        <a href="tel:{{ $contact['phone'] }}" class="text-decoration-none">
                            {{ $contact['phone'] }}
                        </a>
                    </p>
                </div>
                @endif

                @if($contact['email'] ?? false)
                <div class="mb-4">
                    <h5><i class="fas fa-envelope text-primary"></i> Email</h5>
                    <p>
                        <a href="mailto:{{ $contact['email'] }}" class="text-decoration-none">
                            {{ $contact['email'] }}
                        </a>
                    </p>
                </div>
                @endif

                <hr class="my-4">

                <h5 class="mb-3">Ikuti Kami</h5>
                <div class="d-flex gap-3">
                    @if($contact['instagram'] ?? false)
                    <a href="{{ $contact['instagram'] }}" target="_blank" class="btn btn-outline-danger">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                    @endif
                    @if($contact['tiktok'] ?? false)
                    <a href="{{ $contact['tiktok'] }}" target="_blank" class="btn btn-outline-dark">
                        <i class="fab fa-tiktok"></i> TikTok
                    </a>
                    @endif
                    @if($contact['whatsapp'] ?? false)
                    <a href="{{ $contact['whatsapp'] }}" target="_blank" class="btn btn-outline-success">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    @endif
                </div>
                @else
                <div class="alert alert-info">
                    <p>Informasi kontak belum tersedia</p>
                </div>
                @endif
            </div>

            <!-- Map -->
            <div class="col-lg-6">
                <h2 class="mb-4">Lokasi Kami</h2>
                @if($contact['map_url'] ?? false)
                <div class="ratio ratio-16x9 rounded-3 overflow-hidden shadow">
                    <iframe src="{{ $contact['map_url'] }}" allowfullscreen="" loading="lazy"></iframe>
                </div>
                @else
                <div class="alert alert-info">
                    <p>Peta lokasi belum tersedia</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <h2 class="text-center mb-4">Kirim Pesan</h2>
                <form action="#" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">No. Telepon</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subjek</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-paper-plane"></i> Kirim Pesan
                    </button>
                </form>
            </div>
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
