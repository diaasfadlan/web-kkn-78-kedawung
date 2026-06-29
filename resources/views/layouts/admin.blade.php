<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#212529">
    <meta name="description" content="Panel administrasi website KKN.">
    <title>@yield('title', 'Dashboard') — Admin KKN</title>
    @fonts
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="admin-shell bg-light">
    <a class="skip-link" href="#admin-content">Lewati ke konten</a>

    <header>
        <nav class="navbar navbar-expand-xl navbar-light bg-white border-bottom sticky-top shadow-sm" aria-label="Navigasi admin">
            <div class="container-fluid px-3 px-lg-4">
                <a class="navbar-brand fw-bold" href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-shield-halved text-warning me-2"></i>KKN ADMIN
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Buka navigasi admin">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fa-solid fa-gauge-high me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('artikel.*') ? 'active' : '' }}" href="{{ route('artikel.index') }}">Artikel</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('program.*') ? 'active' : '' }}" href="{{ route('program.index') }}">Program</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">Galeri</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('timeline.*') ? 'active' : '' }}" href="{{ route('timeline.index') }}">Timeline</a></li>
                        <li class="nav-item"><a class="nav-link {{ request()->routeIs('group.*') ? 'active' : '' }}" href="{{ route('group.index') }}">Anggota</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('village.*', 'contact.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Pengaturan</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('village.edit', 'main') }}"><i class="fa-solid fa-map-location-dot me-2"></i>Profil Desa</a></li>
                                <li><a class="dropdown-item" href="{{ route('contact.edit', 'main') }}"><i class="fa-solid fa-address-book me-2"></i>Kontak</a></li>
                            </ul>
                        </li>
                    </ul>

                    <div class="d-flex flex-column flex-xl-row gap-2 py-2 py-xl-0">
                        <a class="btn btn-outline-dark btn-sm" href="{{ route('home') }}" target="_blank" rel="noopener">
                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Lihat Situs
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <main id="admin-content">
        @yield('content')
    </main>

    <footer class="border-top bg-white py-3 mt-5">
        <div class="container-fluid px-3 px-lg-4 text-muted small">
            Panel Admin KKN · {{ date('Y') }}
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
