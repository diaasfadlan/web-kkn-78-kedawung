@extends('layouts.admin')

@section('title', 'Edit Profil Desa')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Edit Profil Desa</h1>
        <p class="lead">Kelola informasi desa KKN</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
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
                        <form action="{{ route('village.update', $id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <h4 class="mb-3 text-primary">Informasi Dasar</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Desa *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $village['name'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="postal_code" class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $village['postal_code'] ?? '') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Alamat Desa *</label>
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $village['address'] ?? '') }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="district" class="form-label">Kecamatan *</label>
                                    <input type="text" class="form-control" id="district" name="district" value="{{ old('district', $village['district'] ?? '') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="regency" class="form-label">Kabupaten / Kota *</label>
                                    <input type="text" class="form-control" id="regency" name="regency" value="{{ old('regency', $village['regency'] ?? '') }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="province" class="form-label">Provinsi *</label>
                                    <input type="text" class="form-control" id="province" name="province" value="{{ old('province', $village['province'] ?? '') }}" required>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h4 class="mb-3 text-primary">Detail Profil & Potensi</h4>

                            <div class="mb-3">
                                <label for="history" class="form-label">Sejarah Desa</label>
                                <textarea class="form-control" id="history" name="history" rows="4">{{ old('history', $village['history'] ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="philosophy" class="form-label">Filosofi / Visi Misi Desa</label>
                                <textarea class="form-control" id="philosophy" name="philosophy" rows="4">{{ old('philosophy', $village['philosophy'] ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="demographics" class="form-label">Demografis Desa</label>
                                <textarea class="form-control" id="demographics" name="demographics" rows="4">{{ old('demographics', $village['demographics'] ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="potential" class="form-label">Potensi Desa</label>
                                <textarea class="form-control" id="potential" name="potential" rows="4">{{ old('potential', $village['potential'] ?? '') }}</textarea>
                            </div>

                            <hr class="my-4">
                            <h4 class="mb-3 text-primary">Informasi Kontak</h4>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="contact_phone" class="form-label">No. Telepon</label>
                                    <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $village['contact_phone'] ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="contact_email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $village['contact_email'] ?? '') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="contact_address" class="form-label">Alamat Kantor Desa / Lokasi Google Maps</label>
                                <input type="text" class="form-control" id="contact_address" name="contact_address" value="{{ old('contact_address', $village['contact_address'] ?? '') }}">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
