@extends('layouts.admin')

@section('title', isset($member) ? 'Edit Anggota' : 'Tambah Anggota')

@section('content')
<section class="hero">
    <div class="container">
        <h1>{{ isset($member) ? 'Edit Anggota' : 'Tambah Anggota Kelompok Baru' }}</h1>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
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
                        <form action="{{ isset($member) ? route('group.update', $id) : route('group.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($member))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $member['name'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nim" class="form-label">NIM *</label>
                                    <input type="text" class="form-control" id="nim" name="nim" value="{{ old('nim', $member['nim'] ?? '') }}" required {{ isset($member) ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="prodi" class="form-label">Program Studi *</label>
                                    <input type="text" class="form-control" id="prodi" name="prodi" value="{{ old('prodi', $member['prodi'] ?? '') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">Jabatan di Kelompok *</label>
                                    <input type="text" class="form-control" id="position" name="position" placeholder="Contoh: Ketua, Sekretaris, Anggota" value="{{ old('position', $member['position'] ?? '') }}" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="photo" class="form-label">Foto Anggota</label>
                                <input type="file" class="form-control" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">JPG, PNG, atau WebP. Maksimal 2 MB.</small>
                            </div>

                            <hr class="my-4">
                            <h5 class="text-primary mb-3">Sosial Media (Opsional)</h5>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $member['social_media']['email'] ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram URL</label>
                                <input type="url" class="form-control" id="instagram" name="instagram" placeholder="https://instagram.com/username" value="{{ old('instagram', $member['social_media']['instagram'] ?? '') }}">
                            </div>

                            <div class="mb-3">
                                <label for="whatsapp" class="form-label">No. WhatsApp</label>
                                <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Contoh: 08123456789" value="{{ old('whatsapp', $member['social_media']['whatsapp'] ?? '') }}">
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('group.index') }}" class="btn btn-outline-secondary">Batal</a>
                                <button type="submit" class="btn btn-primary">{{ isset($member) ? 'Update' : 'Simpan' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
