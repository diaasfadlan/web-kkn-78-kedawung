@extends('layouts.admin')

@section('title', 'Pengaturan Profil Kelompok & DPL')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Pengaturan Profil Kelompok & DPL</h1>
        <p class="lead">Kelola informasi umum kelompok KKN dan Dosen Pembimbing Lapangan</p>
    </div>
</section>

<section class="py-5 bg-light">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Perhatian:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('group.setting.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Group Info Card -->
                <div class="col-lg-7">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-users text-warning me-2"></i> Identitas Kelompok KKN</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="group_name" class="form-label fw-bold">Nama Kelompok *</label>
                                <input type="text" class="form-control" id="group_name" name="group_name" value="{{ old('group_name', $group['name'] ?? 'KKN 078 Desa Kedawung') }}" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="university" class="form-label fw-bold">Universitas / Perguruan Tinggi *</label>
                                    <input type="text" class="form-control" id="university" name="university" value="{{ old('university', $group['university'] ?? '') }}" placeholder="Contoh: Universitas Jenderal Soedirman" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label fw-bold">Lokasi KKN *</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $group['location'] ?? 'Desa Kedawung, Banjarnegara') }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="period" class="form-label fw-bold">Periode / Waktu Pelaksanaan *</label>
                                <input type="text" class="form-control" id="period" name="period" value="{{ old('period', $group['period'] ?? '') }}" placeholder="Contoh: Juli - Agustus 2026" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-bold">Deskripsi Singkat Kelompok *</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required>{{ old('description', $group['description'] ?? '') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="group_photo" class="form-label fw-bold">Foto Bersama Kelompok / Banner Utama</label>
                                @if(!empty($group['photo_url']))
                                    <div class="mb-2">
                                        <img src="{{ $group['photo_url'] }}" alt="Foto Kelompok" class="img-fluid rounded border" style="max-height: 200px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="group_photo" name="group_photo" accept="image/*">
                                <small class="text-muted">Format: JPG, PNG, WebP. Kosongkan jika tidak ingin mengubah.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lecturer Info Card -->
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-dark text-white py-3">
                            <h5 class="mb-0"><i class="fas fa-chalkboard-teacher text-info me-2"></i> Dosen Pembimbing Lapangan (DPL)</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label for="lecturer_name" class="form-label fw-bold">Nama Dosen & Gelar</label>
                                <input type="text" class="form-control" id="lecturer_name" name="lecturer_name" value="{{ old('lecturer_name', $lecturer['name'] ?? '') }}" placeholder="Contoh: Dr. Budi Santoso, M.Si.">
                            </div>

                            <div class="mb-3">
                                <label for="lecturer_nidn" class="form-label fw-bold">NIDN / NIP</label>
                                <input type="text" class="form-control" id="lecturer_nidn" name="lecturer_nidn" value="{{ old('lecturer_nidn', $lecturer['nidn'] ?? '') }}" placeholder="Contoh: 0012345678">
                            </div>

                            <div class="mb-3">
                                <label for="lecturer_department" class="form-label fw-bold">Fakultas / Program Studi</label>
                                <input type="text" class="form-control" id="lecturer_department" name="lecturer_department" value="{{ old('lecturer_department', $lecturer['department'] ?? '') }}" placeholder="Contoh: Fakultas Ilmu Sosial & Ilmu Politik">
                            </div>

                            <div class="mb-3">
                                <label for="lecturer_photo" class="form-label fw-bold">Foto Dosen</label>
                                @if(!empty($lecturer['photo_url']))
                                    <div class="mb-2">
                                        <img src="{{ $lecturer['photo_url'] }}" alt="Foto DPL" class="img-fluid rounded border" style="max-height: 180px; object-fit: cover;">
                                    </div>
                                @endif
                                <input type="file" class="form-control" id="lecturer_photo" name="lecturer_photo" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection
