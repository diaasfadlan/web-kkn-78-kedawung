@extends('layouts.admin')

@section('title', 'Pesan Masuk')

@section('content')
<section class="hero">
    <div class="container">
        <h1>Pesan Masuk</h1>
        <p class="lead">Daftar aspirasi, pertanyaan, dan pesan dari pengunjung website</p>
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

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Tanggal</th>
                                <th>Pengirim</th>
                                <th>Subjek</th>
                                <th>Pesan</th>
                                <th class="text-center" style="width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($messages as $message)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <small class="text-muted d-block">
                                        {{ isset($message['created_at']) ? \Carbon\Carbon::parse($message['created_at'])->format('d M Y') : '-' }}
                                    </small>
                                    <small class="text-muted">
                                        {{ isset($message['created_at']) ? \Carbon\Carbon::parse($message['created_at'])->format('H:i') : '' }} WIB
                                    </small>
                                </td>
                                <td>
                                    <strong>{{ $message['name'] ?? 'Anonim' }}</strong><br>
                                    <small><a href="mailto:{{ $message['email'] ?? '' }}" class="text-decoration-none text-muted"><i class="fas fa-envelope"></i> {{ $message['email'] ?? '-' }}</a></small>
                                    @if(!empty($message['phone']))
                                        <br><small><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $message['phone']) }}" target="_blank" class="text-success text-decoration-none"><i class="fab fa-whatsapp"></i> {{ $message['phone'] }}</a></small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary mb-1">Subjek</span><br>
                                    <strong>{{ $message['subject'] ?? '-' }}</strong>
                                </td>
                                <td>
                                    <p class="mb-0 text-muted small" style="white-space: pre-wrap; max-width: 380px;">{{ $message['message'] ?? '-' }}</p>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.messages.destroy', $message['id'] ?? '') }}" method="POST" onsubmit="return confirm('Hapus pesan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pesan">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-0">Belum ada pesan masuk dari pengunjung.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
