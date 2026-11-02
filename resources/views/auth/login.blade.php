<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Masuk | Gudang.in</title>
    @include('partials.theme')
    <style>
        .login { min-height: 100dvh; display: grid; }
        @media (min-width: 1024px) { .login { grid-template-columns: minmax(460px, 6fr) 5fr; } }

        /* ---------- Panel kiri ---------- */
        .hero { display: none; position: relative; overflow: hidden; isolation: isolate; flex-direction: column; justify-content: space-between;
            padding: 2.5rem 3.5rem 3.5rem; color: #EEF3EC; background: #14382A; }
        .dark .hero { background: #0E241B; }
        @media (min-width: 1024px) { .hero { display: flex; } }
        /* kisi titik halus */
        .hero::before { content: ''; position: absolute; inset: 0; z-index: -2;
            background-image: radial-gradient(rgb(255 255 255 / .09) 1px, transparent 1.2px); background-size: 22px 22px;
            mask-image: linear-gradient(180deg, #000 0%, transparent 75%); -webkit-mask-image: linear-gradient(180deg, #000 0%, transparent 75%); }
        .hero-top { display: flex; align-items: center; gap: .7rem; }
        .hero-logo { width: 36px; height: 36px; border-radius: 11px; background: #fff; display: grid; place-items: center; }
        body h2.hero-title { font-size: clamp(2.4rem, 1.4rem + 2.4vw, 3.6rem); line-height: 1.04; font-weight: 620; letter-spacing: -.04em; max-width: 14ch; text-wrap: balance; }
        body h2.hero-title em { font-style: normal; color: #E4B64F; }
        .hero-note { margin-top: 1.1rem; max-width: 34ch; color: rgb(238 243 236 / .66); font-size: 15.5px; line-height: 1.55; }
        .hero-mid { margin-top: auto; padding-top: 4rem; }

        /* rak: kotak-kotak masuk satu per satu */
        .shelves { display: flex; flex-direction: column; gap: 0; margin-top: 3.5rem; }
        .shelf { display: flex; align-items: flex-end; gap: 9px; height: 64px; border-bottom: 1.5px solid rgb(255 255 255 / .18); transform-origin: 0 50%; animation: grow-x 1.2s var(--ease) backwards; }
        .shelf i { display: block; border-radius: 5px 5px 0 0; background: rgb(255 255 255 / .08); border: 1px solid rgb(255 255 255 / .1); border-bottom: 0; animation: stack .9s var(--ease) backwards; }
        .shelf i:nth-child(3n) { background: rgb(255 255 255 / .14); }
        .shelf i.is-brass { background: #E4B64F; border-color: #E4B64F; }
        @keyframes stack { from { transform: translateY(14px) scaleY(.7); opacity: 0; } to { transform: none; opacity: 1; } }

        /* ---------- Panel form ---------- */
        .form-side { display: grid; place-items: center; padding: 1.5rem; position: relative; }
        .rise-in { animation: rise .9s var(--ease) backwards; animation-delay: var(--d, 0ms); }
        .login-card { width: 100%; max-width: 24rem; }
        .field > span { display: block; font-size: 13px; font-weight: 560; margin-bottom: .45rem; color: rgb(var(--ink) / .78); }
        .ico-wrap { position: relative; }
        .ico-wrap > .lead { position: absolute; left: 13px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: rgb(var(--ink) / .4); pointer-events: none; transition: color .2s; }
        .ico-wrap:focus-within > .lead { color: rgb(var(--moss-500)); }
        .ico-wrap input { padding-left: 2.6rem !important; height: 48px; border-radius: 12px; }
        .pw-wrap button { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); }
        .pw-wrap button:active { transform: translateY(-50%) scale(.94) !important; }
        .submit { display: flex; align-items: center; justify-content: center; gap: .5rem; width: 100%; height: 48px; border-radius: 12px; font-size: 15px; font-weight: 580; color: #fff; background: rgb(var(--solid)); }
        .submit:hover { background: rgb(var(--solid-hover)); }
        .submit svg { width: 17px; height: 17px; stroke-width: 2; transition: transform .35s var(--ease); }
        .submit:hover svg { transform: translateX(3px); }
        .divider { height: 1px; background: rgb(var(--ink) / .09); margin: 1.75rem 0 1.25rem; }
        .mobile-brand .hero-logo { background: rgb(var(--solid)); }
    </style>
</head>
<body>
<div class="login">

    {{-- Kiri: sapaan + rak --}}
    <aside class="hero" aria-hidden="true">
        <div class="hero-top rise-in">
            <span class="hero-logo"><svg width="22" height="22" viewBox="0 0 28 28"><path d="M8 10h12M8 14h12M8 18h6.5" stroke="#14382A" stroke-width="2.4" stroke-linecap="round"/><circle cx="19.6" cy="18" r="1.9" fill="#C9962B"/></svg></span>
            <span class="brand-name" style="font-size:1.15rem">Gudang.in</span>
        </div>

        <div class="hero-mid">
            <h2 class="hero-title rise-in" style="--d:120ms">Cek stok tanpa <em>ribet.</em></h2>
            <p class="hero-note rise-in" style="--d:200ms">Stok, barang masuk dan keluar, sampai peminjaman, semuanya dicatat di satu tempat.</p>

            <div class="shelves">
                @php
                    $rows = [
                        [38, 22, 30, 46, 26, 34, 20, 40],
                        [30, 44, 24, 36, 28, 48, 22],
                        [24, 38, 30, 44, 26, 32, 36],
                    ];
                @endphp
                @foreach ($rows as $r => $boxes)
                    <div class="shelf" style="animation-delay: {{ 300 + $r * 140 }}ms; width: {{ 100 - $r * 9 }}%">
                        @foreach ($boxes as $b => $h)
                            <i style="width: {{ 22 + (($b * 7 + $r * 5) % 5) * 7 }}px; height: {{ $h + 8 }}px; animation-delay: {{ 700 + $r * 140 + $b * 70 }}ms" @class(['is-brass' => $r === 1 && $b === 3])></i>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </aside>

    {{-- Kanan: form --}}
    <main class="form-side">
        <button type="button" class="icon-btn theme-toggle fixed top-4 right-4" title="Ganti tema terang/gelap" aria-label="Ganti tema" onclick="window.toggleTheme && window.toggleTheme()">
            <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
        </button>

        <div class="login-card">
            <div class="mobile-brand flex items-center gap-2.5 mb-10 lg:hidden rise-in">
                <span class="hero-logo"><svg width="22" height="22" viewBox="0 0 28 28"><path d="M8 10h12M8 14h12M8 18h6.5" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/><circle cx="19.6" cy="18" r="1.9" fill="#E4B64F"/></svg></span>
                <span class="brand-name" style="font-size:1.15rem">Gudang.in</span>
            </div>

            <h1 class="rise-in text-[2.1rem] font-semibold" style="--d:80ms; letter-spacing:-.04em; line-height:1.1">Selamat datang</h1>
            <p class="rise-in text-ink-900/55 mt-2.5 mb-8" style="--d:140ms">Masuk dengan akun yang diberikan admin kantor.</p>

            @if ($errors->any())
                <div class="flash flash-err rise-in" role="alert" style="--d:0ms">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4" x-data="{ show: false }">
                @csrf
                <label class="field block rise-in" style="--d:200ms">
                    <span>Email</span>
                    <div class="ico-wrap">
                        <svg class="lead" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3.5 7.5l8.5 6 8.5-6"/></svg>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" inputmode="email"
                            class="w-full pr-3.5 text-sm" placeholder="nama@kantor.com">
                    </div>
                </label>
                <label class="field block rise-in" style="--d:260ms">
                    <span>Kata sandi</span>
                    <div class="ico-wrap pw-wrap">
                        <svg class="lead" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="3"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                        <input :type="show ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password"
                            class="w-full pr-12 text-sm" placeholder="Masukkan kata sandi">
                        <button type="button" class="icon-btn !w-9 !h-9" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="show" x-cloak xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3l18 18M10.6 5.1A10.6 10.6 0 0 1 12 5c6.4 0 10 7 10 7a17 17 0 0 1-3.2 4.1M6.5 6.6C3.6 8.4 2 12 2 12s3.6 7 10 7c1.6 0 3-.4 4.3-1M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                        </button>
                    </div>
                </label>
                <label class="flex items-center gap-2.5 text-sm text-ink-900/65 rise-in cursor-pointer select-none" style="--d:320ms">
                    <input type="checkbox" name="remember">
                    Ingat saya di perangkat ini
                </label>
                <button class="submit rise-in" style="--d:380ms">
                    Masuk
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <div class="divider rise-in" style="--d:440ms"></div>
            <p class="rise-in text-[13px] text-ink-900/50" style="--d:480ms">Lupa kata sandi atau belum punya akun? Hubungi admin kantor.</p>
            <p class="rise-in text-xs text-ink-900/35 mt-8" style="--d:520ms">&copy; {{ date('Y') }} Gudang.in</p>
        </div>
    </main>
</div>
@include('partials.motion')
</body>
</html>
