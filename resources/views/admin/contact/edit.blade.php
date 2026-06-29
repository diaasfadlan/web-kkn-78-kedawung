@extends('layouts.admin')

@section('title', 'Edit Informasi Kontak')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Edit Kontak</h1>
        <p class="lead">Kelola informasi kontak kelompok KKN</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Error!</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-body p-4">
                        <form action="{{ route('contact.update', $id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Utama *</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $contact['email'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">No. Telepon / HP *</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $contact['phone'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="whatsapp" class="form-label">No. WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $contact['whatsapp'] ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram URL</label>
                                <input type="url" class="form-control" id="instagram" name="instagram" placeholder="https://instagram.com/..." value="{{ old('instagram', $contact['instagram'] ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="tiktok" class="form-label">TikTok URL</label>
                                <input type="url" class="form-control" id="tiktok" name="tiktok" placeholder="https://tiktok.com/@..." value="{{ old('tiktok', $contact['tiktok'] ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Sekretariat KKN *</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required>{{ old('address', $contact['address'] ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="map_url" class="form-label">Google Maps URL</label>
                                <input type="url" class="form-control" id="map_url" name="map_url" placeholder="https://maps.google.com/..." value="{{ old('map_url', $contact['map_url'] ?? '') }}">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Kontak</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
