@extends('layouts.app')

@section('title', $member['name'] ?? 'Detail Anggota')

@section('content')
<section class="member-detail-page">
    <div class="container">
        <div class="member-detail-intro text-center">
            <p class="eyebrow">Profil anggota KKN</p>
            <h1>Kenalan lebih dekat!</h1>
            <p>Gerakkan mouse atau sentuh ID card untuk melihat efek kartu yang menggantung.</p>
        </div>

        <div class="member-detail-layout">
        <div class="member-id-column">
        <div class="member-id-scene" data-id-scene>
            <div class="lanyard" aria-hidden="true">
                <div class="lanyard-strap"></div>
                <div class="lanyard-ring"></div>
                <div class="lanyard-clip"></div>
            </div>

            <div class="member-id-stack">
                <div class="id-layer id-layer-blue" data-id-layer aria-hidden="true"></div>
                <div class="id-layer id-layer-pink" data-id-layer aria-hidden="true"></div>

                <article class="member-id-card" data-id-card>
                    <div class="id-card-header">
                        <span class="id-card-brand">KKN<span>.</span>KARYA</span>
                        <span class="id-card-year">2026</span>
                    </div>

                    <div class="id-photo-wrap">
                        @if($member['photo_url'] ?? false)
                            <img src="{{ $member['photo_url'] }}" alt="Foto {{ $member['name'] ?? 'anggota' }}" decoding="async">
                        @else
                            <div class="id-photo-placeholder"><i class="fa-solid fa-user"></i></div>
                        @endif
                        <span class="id-position">{{ $member['position'] ?? 'Anggota' }}</span>
                    </div>

                    <div class="id-card-body">
                        <p class="id-label">Nama anggota</p>
                        <h2>{{ $member['name'] ?? 'Anggota KKN' }}</h2>

                        <div class="id-info-grid">
                            <div>
                                <span>NIM</span>
                                <strong>{{ $member['nim'] ?? '-' }}</strong>
                            </div>
                            <div>
                                <span>Program Studi</span>
                                <strong>{{ $member['prodi'] ?? '-' }}</strong>
                            </div>
                        </div>

                        <div class="id-socials">
                            @if($member['social_media']['instagram'] ?? false)
                                <a href="{{ $member['social_media']['instagram'] }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                            @endif
                            @if($member['social_media']['whatsapp'] ?? false)
                                <a href="{{ $member['social_media']['whatsapp'] }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            @endif
                            @if($member['social_media']['email'] ?? false)
                                <a href="mailto:{{ $member['social_media']['email'] }}" aria-label="Email"><i class="fas fa-envelope"></i></a>
                            @endif
                        </div>
                    </div>

                    <div class="id-card-footer">
                        <span>Official Member</span>
                        <div class="barcode" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                    </div>
                </article>
            </div>
        </div>
        </div>

        <aside class="member-info-panel">
            <span class="member-info-number">ID / {{ $member['nim'] ?? 'KKN-2026' }}</span>
            <p class="eyebrow">Data lengkap anggota</p>
            <h2>{{ $member['name'] ?? 'Anggota KKN' }}</h2>
            <span class="member-info-role">{{ $member['position'] ?? 'Anggota' }}</span>

            <div class="member-info-list">
                <div class="member-info-item">
                    <span class="member-info-icon bg-warning"><i class="fa-solid fa-id-badge"></i></span>
                    <div><small>NIM</small><strong>{{ $member['nim'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-info"><i class="fa-solid fa-graduation-cap"></i></span>
                    <div><small>Program Studi</small><strong>{{ $member['prodi'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-secondary"><i class="fa-solid fa-people-group"></i></span>
                    <div><small>Jabatan Kelompok</small><strong>{{ $member['position'] ?? '-' }}</strong></div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-primary"><i class="fa-solid fa-envelope"></i></span>
                    <div>
                        <small>Email</small>
                        @if($member['social_media']['email'] ?? false)
                            <a href="mailto:{{ $member['social_media']['email'] }}">{{ $member['social_media']['email'] }}</a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-danger"><i class="fa-brands fa-instagram"></i></span>
                    <div>
                        <small>Instagram</small>
                        @if($member['social_media']['instagram'] ?? false)
                            <a href="{{ $member['social_media']['instagram'] }}" target="_blank" rel="noopener">Buka Instagram <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
                <div class="member-info-item">
                    <span class="member-info-icon bg-success"><i class="fa-brands fa-whatsapp"></i></span>
                    <div>
                        <small>WhatsApp</small>
                        @if($member['social_media']['whatsapp'] ?? false)
                            <strong>{{ $member['social_media']['whatsapp'] }}</strong>
                        @else
                            <strong>-</strong>
                        @endif
                    </div>
                </div>
            </div>

            <p class="member-info-note"><i class="fa-solid fa-star"></i> Bagian dari tim KKN yang bergerak, belajar, dan berkarya bersama masyarakat.</p>
        </aside>
        </div>

        <div class="text-center member-detail-actions">
            <a href="{{ route('profil-kelompok') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Semua Anggota</a>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
(() => {
    const scene = document.querySelector('[data-id-scene]');
    const card = scene?.querySelector('[data-id-card]');
    const layers = scene ? [...scene.querySelectorAll('[data-id-layer]')] : [];
    const canTilt = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!scene || !card || !canTilt) return;

    let frame = null;
    let pointerX = 0;
    let pointerY = 0;
    let touching = false;

    const paint = () => {
        card.style.transform = `rotateX(${pointerY * -7}deg) rotateY(${pointerX * 10}deg) translate3d(${pointerX * 5}px, ${pointerY * 4}px, 28px)`;
        layers[0]?.style.setProperty('transform', `translate3d(${pointerX * -14}px, ${pointerY * -10}px, -35px) rotate(-6deg)`);
        layers[1]?.style.setProperty('transform', `translate3d(${pointerX * 12}px, ${pointerY * 9}px, -18px) rotate(5deg)`);
        frame = null;
    };

    scene.addEventListener('pointerdown', (event) => {
        touching = event.pointerType !== 'mouse';
        if (touching) scene.setPointerCapture(event.pointerId);
    });

    scene.addEventListener('pointermove', (event) => {
        if (event.pointerType !== 'mouse' && !touching) return;
        const bounds = scene.getBoundingClientRect();
        pointerX = ((event.clientX - bounds.left) / bounds.width - .5) * 2;
        pointerY = ((event.clientY - bounds.top) / bounds.height - .5) * 2;
        if (!frame) frame = requestAnimationFrame(paint);
    });

    const resetTilt = () => {
        touching = false;
        pointerX = 0;
        pointerY = 0;
        if (!frame) frame = requestAnimationFrame(paint);
    };

    scene.addEventListener('pointerup', resetTilt);
    scene.addEventListener('pointercancel', resetTilt);

    scene.addEventListener('pointerleave', resetTilt);
})();
</script>
@endsection
