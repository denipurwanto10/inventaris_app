<!DOCTYPE html>
<html lang="id" x-data="{ sidebarOpen: false, sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === '1' }" x-init="$watch('sidebarCollapsed', v => localStorage.setItem('sidebarCollapsed', v ? '1' : '0'))" :class="{ 'overflow-hidden': sidebarOpen }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dasbor') · Gudang.in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@700;800;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    {{-- Set theme before first paint to avoid a light/dark flash --}}
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var wantsDark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', wantsDark);
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['"Big Shoulders Display"', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        /* "ink" – warm charcoal, steel-and-oxidised-wood, not cold gray */
                        ink: {
                            100: '#E7E2D4',
                            700: '#332C1F',
                            800: '#272217',
                            900: '#1C1812',
                            950: '#120F0A',
                        },
                        /* "moss" – repurposed as PINE: a deep working green (crates, tarps, safety gear) */
                        moss: {
                            50:  '#E9F1E9',
                            300: '#84B98C',
                            400: '#57955F',
                            500: '#427C49',
                            600: '#345F39',
                            700: '#28492C',
                        },
                        /* "amber" – repurposed as MUSTARD: hazard-tape / forklift yellow, not pastel */
                        amber: {
                            50:  '#F9EFD7',
                            300: '#DDAE55',
                            400: '#CB9526',
                            500: '#A97710',
                            600: '#8C630B',
                        },
                        /* "rose" – repurposed as RUST: oxidised-metal red, not candy pink */
                        rose: {
                            50:  '#F5E7DE',
                            200: '#E2B198',
                            400: '#BE6A42',
                            500: '#AC4C2A',
                            600: '#8E3D20',
                            700: '#6E2F19',
                        },
                    },
                    boxShadow: {
                        panel: '0 1px 2px rgba(18,15,10,0.04), 0 8px 20px -14px rgba(18,15,10,0.22)',
                    },
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-thumb { background: #cfc9ba; border-radius: 2px; }
        body { background-color: #EBEAE1; transition: background-color .15s ease; }
        .stat-figure { font-variant-numeric: tabular-nums; font-family: 'JetBrains Mono', monospace !important; }

        /* ============ Signature type treatment ============
           Big Shoulders Display is reserved for real headings and hero
           numbers/names — not the small text-sm section labels used
           throughout the dense data views, which stay in Inter for legibility. */
        h1, .font-display, .text-2xl.font-extrabold:not(.stat-figure), .text-lg.font-extrabold {
            font-family: 'Big Shoulders Display', sans-serif;
            letter-spacing: .01em;
        }
        /* small card/section titles (not hero headings like a person's name) get a stenciled-label treatment */
        h2:not(.text-lg):not(.text-2xl), h3:not(.text-lg):not(.text-2xl) {
            text-transform: uppercase;
            letter-spacing: .06em;
        }
        header h1 {
            font-family: 'Big Shoulders Display', sans-serif;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: 1.25rem;
            line-height: 1.1;
        }
        @media (min-width: 1024px) { header h1 { font-size: 1.5rem; } }

        /* ============ Cards: soft modern panels ============ */
        .bg-white.rounded-xl.border.shadow-panel {
            border-radius: 16px !important;
        }
        .rounded-xl.overflow-x-auto, .rounded-xl.overflow-hidden { border-radius: 16px !important; }
        .rounded-2xl { border-radius: 20px !important; }

        /* ============ Buttons & inputs: soft modern radius ============ */
        .rounded-lg { border-radius: 10px !important; }

        /* ============ Badges: soft pill ============ */
        .rounded-full.bg-moss-50,
        .rounded-full.bg-rose-50,
        .rounded-full.bg-amber-400\/10,
        .rounded-full.bg-ink-900\/5 {
            border-radius: 9999px !important;
        }

        /* ============ Dark mode ============
           The rest of the app is built with plain utility classes (bg-white,
           text-ink-900/60, border-ink-900/8, shadow-panel, …). Rather than
           editing dark: variants into every single view, we override the
           exact palette this app uses whenever the <html> has .dark on it. */
        .dark ::-webkit-scrollbar-thumb { background: #3A3324; }
        .dark body { background-color: #100D08; }

        .dark .bg-white { background-color: #201B12 !important; }
        .dark .bg-white\/90 { background-color: rgba(32, 27, 18, 0.88) !important; }
        .dark .bg-white\/5 { background-color: rgba(255, 255, 255, .05) !important; }

        .dark .bg-ink-900\/5 { background-color: rgba(255, 255, 255, .06) !important; }
        .dark .bg-ink-900\/\[0\.02\] { background-color: rgba(255, 255, 255, .025) !important; }
        .dark .bg-ink-900\/\[0\.03\] { background-color: rgba(255, 255, 255, .04) !important; }

        .dark .rounded-full.bg-moss-50 { background-color: rgba(87, 149, 95, .2) !important; }
        .dark .rounded-full.bg-rose-50 { background-color: rgba(172, 76, 42, .2) !important; }
        .dark .rounded-full.bg-amber-400\/10 { background-color: rgba(169, 119, 16, .22) !important; }

        /* Same tints as above, but for the (more common) non-pill usages:
           flash message banners, notification unread highlight, hover states.
           These were previously only matched when paired with .rounded-full,
           so success/error banners and hover backgrounds stayed light-mode
           bright and low-contrast against dark surfaces. */
        .dark .bg-moss-50 { background-color: rgba(87, 149, 95, .2) !important; }
        .dark .bg-moss-50\/40 { background-color: rgba(87, 149, 95, .12) !important; }
        .dark .hover\:bg-moss-50:hover { background-color: rgba(87, 149, 95, .2) !important; }
        .dark .bg-rose-50 { background-color: rgba(172, 76, 42, .2) !important; }
        .dark .hover\:bg-rose-50:hover { background-color: rgba(172, 76, 42, .2) !important; }
        .dark .hover\:bg-amber-50:hover { background-color: rgba(169, 119, 16, .22) !important; }

        /* "Persetujuan" badge uses Tailwind's default sky color, which isn't
           part of this app's custom dark-aware palette. */
        .dark .bg-sky-50 { background-color: rgba(14, 165, 233, .18) !important; }
        .dark .text-sky-600 { color: #7DD3FC !important; }

        .dark .border-ink-900\/5 { border-color: rgba(255, 255, 255, .05) !important; }
        .dark .border-ink-900\/8 { border-color: rgba(255, 255, 255, .08) !important; }
        .dark .border-ink-900\/10 { border-color: rgba(255, 255, 255, .10) !important; }
        .dark .border-ink-900\/15 { border-color: rgba(255, 255, 255, .15) !important; }
        .dark .border-ink-900\/20 { border-color: rgba(255, 255, 255, .20) !important; }
        .dark .border-moss-300 { border-color: rgba(87, 149, 95, .55) !important; }
        .dark .border-rose-200 { border-color: rgba(172, 76, 42, .5) !important; }
        /* "divide" utilities generate their own class name (not "border-…"),
           so the border-ink-900/5 rule above never reached table/list dividers. */
        .dark .divide-ink-900\/5 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(255, 255, 255, .05) !important; }

        .dark .text-ink-900 { color: #ECE6D6 !important; }
        .dark .text-ink-900\/30 { color: rgba(236, 230, 214, .38) !important; }
        .dark .text-ink-900\/40 { color: rgba(236, 230, 214, .45) !important; }
        .dark .text-ink-900\/50 { color: rgba(236, 230, 214, .52) !important; }
        .dark .text-ink-900\/60 { color: rgba(236, 230, 214, .62) !important; }
        .dark .text-ink-900\/70 { color: rgba(236, 230, 214, .72) !important; }
        .dark .text-ink-900\/80 { color: rgba(236, 230, 214, .82) !important; }
        .dark .text-moss-700 { color: #84B98C !important; }
        .dark .text-rose-700 { color: #E2B198 !important; }
        .dark .hover\:bg-ink-900\/\[0\.02\]:hover { background-color: rgba(255, 255, 255, .04) !important; }
        .dark .hover\:bg-ink-900\/\[0\.015\]:hover { background-color: rgba(255, 255, 255, .03) !important; }

        /* Primary "black" CTA buttons (bg-ink-950 + text-white — new item,
           save, active filter tab, admin badge…) sit almost exactly on top
           of the dark-mode page background (#100D08 vs #120F0A), so they
           nearly disappear in dark mode. Swap them to the brand's pine/green
           accent, which is already used and proven readable elsewhere
           (e.g. the header's "Barang Baru" button). This intentionally does
           NOT touch the sidebar or modal overlays, which also use ink-950
           but without the text-white pairing. */
        .dark .bg-ink-950.text-white { background-color: #345F39 !important; border-color: #345F39 !important; }
        .dark .border-ink-950 { border-color: #345F39 !important; }
        .dark .text-white.hover\:bg-ink-900:hover { background-color: #28492C !important; }

        .dark .shadow-panel { box-shadow: 0 1px 0 rgba(0,0,0,.3), 0 12px 26px -16px rgba(0,0,0,.55); }
        .dark .shadow-2xl { box-shadow: 0 20px 45px rgba(0,0,0,.6); }

        .dark input, .dark select, .dark textarea {
            background-color: #171208;
            border-color: rgba(255, 255, 255, .15);
            color: #ECE6D6;
            color-scheme: dark;
        }
        .dark input::placeholder, .dark textarea::placeholder { color: rgba(236, 230, 214, .35); }
        .dark input[type="checkbox"] { background-color: #171208; }

        /* theme toggle icon swap */
        .theme-toggle .icon-sun { display: none; }
        .dark .theme-toggle .icon-sun { display: block; }
        .dark .theme-toggle .icon-moon { display: none; }
    </style>
    @stack('head')
</head>
<body class="font-sans text-ink-900 antialiased">
<div class="min-h-screen lg:flex">

    {{-- Sidebar --}}
    <aside
        x-cloak
        :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0', sidebarCollapsed ? 'lg:w-0 lg:border-r-0' : 'lg:w-72']"
        class="fixed inset-y-0 left-0 z-40 w-72 bg-ink-950 text-ink-100 transform transition-all duration-200 ease-out flex flex-col overflow-hidden shrink-0"
    >
        <div class="w-72 h-full flex flex-col">
            <div class="h-16 flex items-center gap-2.5 px-6 border-b border-white/10 shrink-0">
                <div class="h-9 w-9 shrink-0 bg-amber-400 grid place-items-center font-display font-black text-ink-950 text-base rounded-lg">G</div>
                <div class="min-w-0 flex-1">
                    <p class="font-display font-extrabold text-white leading-none tracking-wide text-lg uppercase truncate">Gudang.in</p>
                    <p class="text-[10px] text-white/40 leading-tight uppercase tracking-widest mt-0.5">Inventaris &amp; Aset Kantor</p>
                </div>
                <button @click="sidebarCollapsed = true" title="Sembunyikan sidebar" class="hidden lg:grid place-items-center h-7 w-7 rounded-lg text-white/40 hover:text-white hover:bg-white/10 transition shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1.5 text-sm">
                <div x-data="{ open: {{ (request()->routeIs('dashboard') || request()->routeIs('reports.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-section-btn">
                        <span>Ringkasan</span>
                        <svg :class="open ? 'rotate-90' : ''" class="h-3.5 w-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-0.5 pb-2">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">Dasbor</a>
                        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'nav-active' : '' }}">Laporan</a>
                    </div>
                </div>

                <div x-data="{ open: {{ (request()->routeIs('items.*') || request()->routeIs('categories.*') || request()->routeIs('suppliers.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-section-btn">
                        <span>Data Master</span>
                        <svg :class="open ? 'rotate-90' : ''" class="h-3.5 w-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-0.5 pb-2">
                        <a href="{{ route('items.index') }}" class="nav-link {{ request()->routeIs('items.*') ? 'nav-active' : '' }}">Barang</a>
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'nav-active' : '' }}">Kategori</a>
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'nav-active' : '' }}">Supplier</a>
                    </div>
                </div>

                <div x-data="{ open: {{ (request()->routeIs('stock-in.*') || request()->routeIs('stock-out.*') || request()->routeIs('stock-transfer.*') || request()->routeIs('loans.*') || request()->routeIs('maintenance.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-section-btn">
                        <span>Transaksi Stok</span>
                        <svg :class="open ? 'rotate-90' : ''" class="h-3.5 w-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-0.5 pb-2">
                        <a href="{{ route('stock-in.index') }}" class="nav-link {{ request()->routeIs('stock-in.*') ? 'nav-active' : '' }}">Barang Masuk</a>
                        <a href="{{ route('stock-out.index') }}" class="nav-link {{ request()->routeIs('stock-out.*') ? 'nav-active' : '' }}">Barang Keluar</a>
                        <a href="{{ route('stock-transfer.index') }}" class="nav-link {{ request()->routeIs('stock-transfer.*') ? 'nav-active' : '' }}">Perpindahan Gudang</a>
                        <a href="{{ route('loans.index') }}" class="nav-link {{ request()->routeIs('loans.*') ? 'nav-active' : '' }}">Peminjaman</a>
                        <a href="{{ route('maintenance.index') }}" class="nav-link {{ request()->routeIs('maintenance.*') ? 'nav-active' : '' }}">Maintenance</a>
                    </div>
                </div>

                <div x-data="{ open: {{ (request()->routeIs('barcode.*') || request()->routeIs('notifications.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-section-btn">
                        <span>Alat</span>
                        <svg :class="open ? 'rotate-90' : ''" class="h-3.5 w-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-0.5 pb-2">
                        <a href="{{ route('barcode.scanner') }}" class="nav-link {{ request()->routeIs('barcode.*') ? 'nav-active' : '' }}">Pindai Barcode / QR</a>
                        <a href="{{ route('notifications.index') }}" class="nav-link {{ request()->routeIs('notifications.*') ? 'nav-active' : '' }} flex items-center justify-between">
                            <span>Notifikasi</span>
                            @if (($unreadNotifCount ?? 0) > 0)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full bg-amber-400 text-ink-950">{{ $unreadNotifCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                @if (auth()->user()->isAdmin())
                <div x-data="{ open: {{ (request()->routeIs('warehouses.*') || request()->routeIs('users.*')) ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="nav-section-btn">
                        <span>Administrasi</span>
                        <svg :class="open ? 'rotate-90' : ''" class="h-3.5 w-3.5 transition-transform" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
                    </button>
                    <div x-show="open" x-collapse x-cloak class="space-y-0.5 pb-2">
                        <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'nav-active' : '' }}">Gudang</a>
                        <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'nav-active' : '' }}">Pengguna</a>
                    </div>
                </div>
                @endif
            </nav>

            <div class="p-3 border-t border-white/10 shrink-0">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-white/5">
                    <div class="h-8 w-8 rounded-full bg-amber-400 grid place-items-center text-ink-950 font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                        <p class="text-white/40 text-[11px] truncate uppercase tracking-wide">{{ ucfirst(auth()->user()->role ?? 'staff') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-white/50 hover:text-white transition" title="Keluar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false" class="fixed inset-0 bg-ink-950/50 z-30 lg:hidden"></div>

    {{-- Main --}}
    <div class="flex-1 min-w-0 transition-all duration-200" :class="sidebarCollapsed ? 'lg:ml-0' : 'lg:ml-72'">
        <header class="h-16 sticky top-0 z-20 bg-white/90 backdrop-blur border-b border-ink-900/8 flex items-center gap-3 px-4 lg:px-8">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-ink-900/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <button x-show="sidebarCollapsed" x-cloak @click="sidebarCollapsed = false" title="Tampilkan sidebar" class="hidden lg:grid place-items-center p-2 -ml-2 rounded-lg hover:bg-ink-900/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
            </button>
            <div class="min-w-0">
                <h1 class="truncate">@yield('title', 'Dasbor')</h1>
                @hasSection('subtitle')
                    <p class="text-xs text-ink-900/50 truncate -mt-0.5">@yield('subtitle')</p>
                @endif
            </div>
            <div class="ml-auto flex items-center gap-2">
                <div class="relative" x-data="{ bellOpen: false }">
                    <button @click="bellOpen = !bellOpen" type="button" title="Notifikasi"
                        class="relative p-2 rounded-lg border border-ink-900/10 hover:bg-ink-900/5 transition shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        @if (($unreadNotifCount ?? 0) > 0)
                            <span class="absolute -top-1 -right-1 h-4 min-w-[16px] px-0.5 rounded-full bg-rose-500 text-white text-[9px] font-bold grid place-items-center">{{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}</span>
                        @endif
                    </button>
                    <div x-show="bellOpen" x-cloak @click.outside="bellOpen=false"
                        class="fixed inset-x-3 top-[4.5rem] w-auto sm:absolute sm:inset-x-auto sm:top-auto sm:right-0 sm:mt-2 sm:w-80 sm:max-w-[90vw] bg-white rounded-xl border border-ink-900/8 shadow-2xl overflow-hidden z-30">
                        <div class="px-4 py-3 border-b border-ink-900/8 flex items-center justify-between">
                            <p class="font-bold text-sm">Notifikasi</p>
                            <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-moss-600 hover:underline">Lihat Semua</a>
                        </div>
                        <div class="max-h-80 overflow-y-auto divide-y divide-ink-900/5">
                            @forelse (($latestNotifs ?? []) as $n)
                            <form action="{{ route('notifications.read', $n) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-3 flex items-start gap-2 hover:bg-ink-900/[0.02] {{ $n->read_at ? '' : 'bg-moss-50/40' }}">
                                    <span class="mt-1.5 h-1.5 w-1.5 rounded-full shrink-0 {{ $n->read_at ? 'bg-ink-900/10' : 'bg-moss-500' }}"></span>
                                    <span class="flex-1 min-w-0">
                                        <span class="block text-xs font-semibold truncate">{{ $n->title }}</span>
                                        <span class="block text-[11px] text-ink-900/50 truncate">{{ $n->message }}</span>
                                    </span>
                                </button>
                            </form>
                            @empty
                            <p class="px-4 py-6 text-center text-xs text-ink-900/40">Tidak ada notifikasi baru.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <button
                    type="button"
                    title="Ganti tema terang/gelap"
                    onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');"
                    class="theme-toggle p-2 rounded-lg border border-ink-900/10 hover:bg-ink-900/5 transition shrink-0"
                >
                    <svg class="icon-sun h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
                    <svg class="icon-moon h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
                </button>
                <a href="{{ route('barcode.scanner') }}" class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg border border-ink-900/10 hover:bg-ink-900/5 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2"/><path d="M7 12h10"/></svg>
                    Pindai
                </a>
                <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-lg bg-moss-600 text-white hover:bg-moss-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
                    <span class="hidden sm:inline">Barang Baru</span>
                </a>
            </div>
        </header>

        <main class="p-4 lg:p-8 max-w-7xl mx-auto">
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-moss-300 bg-moss-50 text-moss-700 text-sm px-4 py-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-5 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 text-sm px-4 py-3 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<style>
    .nav-link {
        display: flex; align-items: center;
        padding: .5rem .75rem;
        margin-bottom: 1px;
        border-radius: 8px;
        color: rgba(255,255,255,.5);
        font-weight: 600;
        font-size: .8125rem;
        letter-spacing: .01em;
        border-left: 2px solid transparent;
        transition: color .15s ease, background-color .15s ease, border-color .15s ease;
    }
    .nav-link:hover { color: #fff; background-color: rgba(255,255,255,.04); }
    .nav-active { background-color: rgba(203,149,38,.12); color: #fff; border-left-color: #CB9526; }

    .nav-section-btn {
        display: flex; align-items: center; justify-content: space-between;
        width: 100%;
        padding: .5rem .75rem;
        font-size: .6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .13em;
        color: rgba(255,255,255,.35);
        border-radius: 8px;
        transition: color .15s ease, background-color .15s ease;
    }
    .nav-section-btn:hover { color: rgba(255,255,255,.7); background-color: rgba(255,255,255,.03); }
</style>

@stack('scripts')
</body>
</html>
