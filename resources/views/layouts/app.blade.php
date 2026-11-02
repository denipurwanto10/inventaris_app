<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Dasbor') | Gudang.in</title>
    @include('partials.theme')
    @stack('head')
</head>
@php
    $i = [
        'dashboard'   => '<rect x="3" y="3" width="7" height="9" rx="1.6"/><rect x="14" y="3" width="7" height="5" rx="1.6"/><rect x="14" y="12" width="7" height="9" rx="1.6"/><rect x="3" y="16" width="7" height="5" rx="1.6"/>',
        'reports'     => '<path d="M4 20v-6M10 20V6M16 20v-9M21 20H3"/>',
        'items'       => '<path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3.3 7.5L12 12.5l8.7-5M12 12.5V21"/>',
        'categories'  => '<path d="M12.6 2.6A2 2 0 0 0 11.2 2H4a2 2 0 0 0-2 2v7.2a2 2 0 0 0 .6 1.4l8.7 8.7a2.4 2.4 0 0 0 3.4 0l6.6-6.6a2.4 2.4 0 0 0 0-3.4z"/><circle cx="7.5" cy="7.5" r="1"/>',
        'suppliers'   => '<path d="M14 18V6a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.7a1 1 0 0 0-.2-.6l-3.3-4.1a1 1 0 0 0-.8-.4H14"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/>',
        'stock-in'    => '<path d="M12 3v12M7 10l5 5 5-5M4 21h16"/>',
        'stock-out'   => '<path d="M12 21V9M7 14l5-5 5 5M4 3h16"/>',
        'transfer'    => '<path d="M7 7h13l-3-3M17 17H4l3 3"/>',
        'loans'       => '<rect x="5" y="3" width="14" height="18" rx="2.2"/><path d="M9 8h6M9 12h6M9 16h3"/>',
        'maintenance' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'scan'        => '<path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 12h10"/>',
        'bell'        => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'warehouses'  => '<path d="M3 21V9l9-6 9 6v12"/><path d="M7 21v-8h10v8M7 17h10"/>',
        'users'       => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c.6-3.4 3.2-5.5 6.5-5.5s5.9 2.1 6.5 5.5"/><path d="M16 4.6a3.5 3.5 0 0 1 0 6.8M18 14.8c2 .7 3.2 2.5 3.5 5.2"/>',
        'search'      => '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'chevron'     => '<path d="M9 6l6 6-6 6"/>',
        'sun'         => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>',
        'menu'        => '<path d="M4 7h16M4 12h16M4 17h10"/>',
    ];
    $unread = $unreadNotifCount ?? 0;
    $groups = [
        [null, [
            ['Dasbor', 'dashboard', 'dashboard', 'dashboard'],
            ['Laporan', 'reports.index', 'reports.*', 'reports'],
        ]],
        ['Data master', [
            ['Barang', 'items.index', 'items.*', 'items'],
            ['Kategori', 'categories.index', 'categories.*', 'categories'],
            ['Supplier', 'suppliers.index', 'suppliers.*', 'suppliers'],
        ]],
        ['Transaksi', [
            ['Barang masuk', 'stock-in.index', 'stock-in.*', 'stock-in'],
            ['Barang keluar', 'stock-out.index', 'stock-out.*', 'stock-out'],
            ['Perpindahan gudang', 'stock-transfer.index', 'stock-transfer.*', 'transfer'],
            ['Peminjaman', 'loans.index', 'loans.*', 'loans'],
            ['Maintenance', 'maintenance.index', 'maintenance.*', 'maintenance'],
        ]],
        ['Alat', [
            ['Pindai barcode / QR', 'barcode.scanner', 'barcode.*', 'scan'],
            ['Notifikasi', 'notifications.index', 'notifications.*', 'bell', $unread],
        ]],
    ];
    if (auth()->user()->isAdmin()) {
        $groups[] = ['Administrasi', [
            ['Gudang', 'warehouses.index', 'warehouses.*', 'warehouses'],
            ['Pengguna', 'users.index', 'users.*', 'users'],
        ]];
    }
    $crumbRoot = 'Gudang.in';
    foreach ($groups as [$gh, $gl]) { foreach ($gl as $gx) { if (request()->routeIs($gx[2])) { $crumbRoot = $gh ?: 'Gudang.in'; } } }
    $svg = fn ($k, $cls = '') => '<svg class="'.$cls.'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$i[$k].'</svg>';
@endphp
<body class="font-sans antialiased"
      x-data="{ drawer: false, toggleSide() { const d = document.documentElement; const c = d.getAttribute('data-side') === 'collapsed'; c ? d.removeAttribute('data-side') : d.setAttribute('data-side', 'collapsed'); try { localStorage.setItem('sidebarCollapsed', c ? '0' : '1') } catch (e) {} } }"
      x-effect="document.documentElement.style.overflow = drawer ? 'hidden' : ''"
      @keydown.escape.window="drawer = false">

{{-- Sidebar --}}
<aside class="app-side" :class="{ 'is-open': drawer }" aria-label="Navigasi utama">
    <div class="brand">
        <a href="{{ route('dashboard') }}" class="brand-link">
            <span class="brand-logo"><svg width="34" height="34" viewBox="0 0 28 28" aria-hidden="true"><rect width="28" height="28" rx="9" fill="rgb(var(--solid))"/><path d="M8 10h12M8 14h12M8 18h6.5" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="19.6" cy="18" r="1.7" fill="#E4B64F"/></svg></span>
            <span class="brand-text"><span class="brand-name">Gudang.in</span><small class="brand-sub">Inventaris kantor</small></span>
        </a>
        <button type="button" @click="toggleSide()" class="icon-btn ml-auto hidden lg:grid" title="Sembunyikan sidebar" aria-label="Sembunyikan sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/></svg>
        </button>
        <button type="button" @click="drawer = false" class="icon-btn ml-auto lg:hidden" aria-label="Tutup menu">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </div>

    <nav class="nav-scroll">
        <span class="nav-indicator" aria-hidden="true"></span>
        @foreach ($groups as [$heading, $links])
            <div class="nav-group">
                @if ($heading)<p class="nav-heading">{{ $heading }}</p>@endif
                @foreach ($links as $l)
                    @php $active = request()->routeIs($l[2]); @endphp
                    <a href="{{ route($l[1]) }}" class="nav-link {{ $active ? 'is-active' : '' }}" @if ($active) aria-current="page" @endif>
                        {!! $svg($l[3]) !!}
                        <span class="truncate">{{ $l[0] }}</span>
                        @if (($l[4] ?? 0) > 0)<span class="count">{{ $l[4] }}</span>@endif
                    </a>
                @endforeach
            </div>
        @endforeach
    </nav>

    <div class="side-user">
        <div class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
        <div class="min-w-0 flex-1 leading-tight">
            <p class="text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
            <p class="text-xs text-ink-900/50 truncate">{{ ucfirst(auth()->user()->role ?? 'staff') }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="icon-btn" title="Keluar" aria-label="Keluar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
            </button>
        </form>
    </div>
</aside>

<div x-show="drawer" x-cloak class="scrim lg:hidden" @click="drawer = false"
     x-transition:enter="transition-opacity duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

{{-- Konten --}}
<div class="app-main">
    <header class="app-head">
        <a href="{{ route('dashboard') }}" class="lg:hidden shrink-0" aria-label="Dasbor">
            <span class="brand-logo"><svg width="32" height="32" viewBox="0 0 28 28" aria-hidden="true"><rect width="28" height="28" rx="9" fill="rgb(var(--solid))"/><path d="M8 10h12M8 14h12M8 18h6.5" stroke="#fff" stroke-width="2" stroke-linecap="round"/><circle cx="19.6" cy="18" r="1.7" fill="#E4B64F"/></svg></span>
        </a>
        <button type="button" @click="toggleSide()" class="icon-btn side-show" title="Tampilkan sidebar" aria-label="Tampilkan sidebar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/></svg>
        </button>

        {{-- Judul ringkas (mobile) --}}
        <p class="head-title ml-1.5 min-w-0 lg:hidden">@yield('title', 'Dasbor')</p>
        {{-- Breadcrumb (desktop) --}}
        <nav class="crumb" aria-label="Lokasi">
            <span class="crumb-root">{{ $crumbRoot }}</span>
            {!! $svg('chevron', 'crumb-sep') !!}
            <span class="crumb-cur">@yield('title', 'Dasbor')</span>
        </nav>

        {{-- Pencarian cepat: tekan / atau Ctrl+K --}}
        <form action="{{ route('items.index') }}" method="GET" role="search" class="hsearch"
              x-data
              @keydown.window="if ((($event.key === '/' && !['INPUT','TEXTAREA','SELECT'].includes($event.target.tagName) && !$event.target.isContentEditable) || (($event.metaKey || $event.ctrlKey) && $event.key.toLowerCase() === 'k'))) { $event.preventDefault(); $refs.q.focus(); $refs.q.select() }">
            {!! $svg('search', 'hs-ico') !!}
            <input x-ref="q" type="search" name="q" value="{{ request()->routeIs('items.index') ? request('q') : '' }}" placeholder="Cari barang, kode, barcode" autocomplete="off" aria-label="Cari barang" enterkeyhint="search">
            <kbd class="hs-kbd" aria-hidden="true">/</kbd>
        </form>

        <div class="head-actions">
            <a href="{{ route('items.index') }}" class="icon-btn md:hidden" aria-label="Cari barang">{!! $svg('search') !!}</a>

            <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" class="icon-btn" title="Notifikasi" aria-label="Notifikasi" :aria-expanded="open">
                    {!! $svg('bell') !!}
                    @if ($unread > 0)<span class="bell-dot">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" class="popover"
                     x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="px-4 py-3 border-b border-ink-900/8 flex items-center justify-between">
                        <p class="font-semibold text-sm">Notifikasi</p>
                        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-moss-600 hover:underline">Lihat semua</a>
                    </div>
                    <div class="max-h-80 overflow-y-auto divide-y divide-ink-900/5">
                        @forelse (($latestNotifs ?? []) as $n)
                        <form action="{{ route('notifications.read', $n) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-3 flex items-start gap-3 hover:bg-ink-900/[0.03] {{ $n->read_at ? '' : 'bg-moss-50/40' }}">
                                <span class="mt-2 h-1.5 w-1.5 rounded-full shrink-0 {{ $n->read_at ? 'bg-ink-900/15' : 'bg-moss-500' }}"></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium truncate">{{ $n->title }}</span>
                                    <span class="block text-xs text-ink-900/50 truncate">{{ $n->message }}</span>
                                </span>
                            </button>
                        </form>
                        @empty
                        <p class="px-4 py-8 text-center text-sm text-ink-900/45">Tidak ada notifikasi baru.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <button type="button" class="icon-btn theme-toggle" title="Ganti tema terang/gelap" aria-label="Ganti tema" onclick="window.toggleTheme && window.toggleTheme()">
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
            </button>

            <span class="head-sep hidden md:block" aria-hidden="true"></span>

            <a href="{{ route('barcode.scanner') }}" class="btn-quiet hidden md:inline-flex">
                {!! $svg('scan') !!} Pindai
            </a>
            <a href="{{ route('items.create') }}" class="btn-primary" aria-label="Barang baru">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                <span class="hidden sm:inline">Barang baru</span>
            </a>

            {{-- Menu akun --}}
            <div class="relative hidden sm:block" x-data="{ open: false }" @keydown.escape.window="open = false">
                <button type="button" @click="open = !open" class="avatar-btn" aria-label="Menu akun" :aria-expanded="open">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </button>
                <div x-show="open" x-cloak @click.outside="open = false" class="popover popover-sm"
                     x-transition:enter="transition duration-300 ease-out" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <div class="acct-head">
                        <span class="avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                        <span class="min-w-0 leading-tight">
                            <span class="block text-sm font-semibold truncate">{{ auth()->user()->name ?? 'Pengguna' }}</span>
                            <span class="block text-xs text-ink-900/50 truncate">{{ auth()->user()->email ?? ucfirst(auth()->user()->role ?? 'staff') }}</span>
                        </span>
                    </div>
                    <div class="p-1.5">
                        <button type="button" class="menu-item" onclick="window.toggleTheme && window.toggleTheme()">
                            <span class="mi-ico"><svg class="mi-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">{!! $i['sun'] !!}</svg></span>
                            Ganti tema
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="menu-item is-danger">
                                <span class="mi-ico"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="app-content mx-auto w-full max-w-[1240px] px-4 sm:px-6 lg:px-10 pt-6 lg:pt-8 pb-32 lg:pb-20">
        <div class="page-head">
            <h1 class="page-title">@yield('title', 'Dasbor')</h1>
            @hasSection('subtitle')<p class="page-sub">@yield('subtitle')</p>@endif
        </div>

        @if (session('success'))
            <div class="flash flash-ok" data-autohide role="status">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="flash flash-err" role="alert">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

{{-- Bilah bawah untuk layar kecil --}}
@php $on = fn ($p) => request()->routeIs($p) ? 'is-active' : ''; @endphp
<nav class="dock" aria-label="Pintasan">
    <a href="{{ route('dashboard') }}" class="{{ $on('dashboard') }}">{!! $svg('dashboard') !!}<span>Dasbor</span></a>
    <a href="{{ route('items.index') }}" class="{{ $on('items.*') }}">{!! $svg('items') !!}<span>Barang</span></a>
    <a href="{{ route('barcode.scanner') }}" class="dock-scan {{ $on('barcode.*') }}" aria-label="Pindai">
        <span class="disc">{!! $svg('scan') !!}</span><small>Pindai</small>
    </a>
    <a href="{{ route('loans.index') }}" class="{{ $on('loans.*') }}">{!! $svg('loans') !!}<span>Pinjam</span></a>
    <button type="button" @click="drawer = true" aria-label="Buka menu">{!! $svg('menu') !!}<span>Menu</span></button>
</nav>

@include('partials.motion')
@include('partials.select')
@stack('scripts')
</body>
</html>
