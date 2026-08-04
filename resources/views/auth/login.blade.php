<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · Gudang.in</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var wantsDark = stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', wantsDark);
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: {
            fontFamily: { sans: ['Inter', 'sans-serif'], display: ['"Big Shoulders Display"', 'sans-serif'] },
            colors: {
                ink: { 950:'#120F0A', 900:'#1C1812' },
                moss: { 50:'#E9F1E9', 300:'#84B98C', 500:'#427C49', 600:'#345F39', 700:'#28492C' },
                amber: { 400:'#CB9526' },
                rose: { 50:'#F5E7DE', 200:'#E2B198', 700:'#6E2F19' },
            }
        } } }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .wordmark { font-family: 'Big Shoulders Display', sans-serif; }
        .crate-lines {
            background-image: repeating-linear-gradient(135deg, rgba(203,149,38,.06) 0 2px, transparent 2px 14px);
        }
        .dark .bg-white { background-color: #201B12 !important; }
        .dark .text-ink-900 { color: #ECE6D6 !important; }
        .dark .text-ink-900\/50 { color: rgba(236,230,214,.55) !important; }
        .dark .text-ink-900\/60 { color: rgba(236,230,214,.62) !important; }
        .dark .text-ink-900\/70 { color: rgba(236,230,214,.72) !important; }
        .dark .border-ink-900\/15 { border-color: rgba(255,255,255,.15) !important; }
        .dark .border-ink-900\/20 { border-color: rgba(255,255,255,.20) !important; }
        .dark .border-rose-200 { border-color: rgba(172,76,42,.4) !important; }
        .dark .bg-rose-50 { background-color: rgba(172,76,42,.18) !important; }
        .dark input {
            background-color: #171208; border-color: rgba(255,255,255,.15); color: #ECE6D6; color-scheme: dark;
        }
        .dark input::placeholder { color: rgba(236,230,214,.35); }
    </style>
</head>
<body class="bg-ink-950 min-h-screen flex items-center justify-center p-4 crate-lines relative overflow-hidden">
    <button
        type="button"
        title="Ganti tema terang/gelap"
        onclick="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');"
        class="fixed top-4 right-4 p-2 rounded-sm border border-white/15 text-white/60 hover:text-white hover:bg-white/5 transition z-10"
    >
        <svg class="dark:hidden h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
        <svg class="hidden dark:block h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
    </button>

    <div class="w-full max-w-sm relative z-0">
        <div class="flex items-center justify-center gap-3 mb-8">
            <div class="h-10 w-10 shrink-0 bg-amber-400 grid place-items-center font-black text-ink-950 wordmark text-lg" style="clip-path: polygon(0 0, 100% 0, 100% 78%, 78% 100%, 0 100%);">G</div>
            <div>
                <p class="wordmark font-extrabold text-white leading-none tracking-wide text-2xl uppercase">Gudang.in</p>
                <p class="text-[10px] text-white/40 leading-tight uppercase tracking-[0.15em] mt-1">Inventaris &amp; Aset Kantor</p>
            </div>
        </div>

        <div class="bg-white rounded-sm border-l-4 border-amber-400 shadow-2xl p-7">
            <h1 class="wordmark text-2xl uppercase tracking-wide font-extrabold text-ink-900">Masuk ke Akun</h1>
            <p class="text-sm text-ink-900/50 mt-1 mb-6">Kelola stok, aset, dan peminjaman kantor.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-sm border border-rose-200 bg-rose-50 text-rose-700 text-sm px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wide text-ink-900/60 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full rounded-sm border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40 focus:border-moss-500" placeholder="nama@kantor.com">
                </div>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wide text-ink-900/60 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required
                        class="w-full rounded-sm border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40 focus:border-moss-500" placeholder="••••••••">
                </div>
                <label class="flex items-center gap-2 text-xs text-ink-900/60">
                    <input type="checkbox" name="remember" class="rounded-sm border-ink-900/20 text-moss-600 focus:ring-moss-500/40">
                    Ingat saya di perangkat ini
                </label>
                <button class="w-full bg-moss-600 hover:bg-moss-700 text-white font-bold text-sm uppercase tracking-wider rounded-sm py-2.5 transition">
                    Masuk
                </button>
            </form>
        </div>
        <p class="text-center text-white/30 text-xs mt-6">&copy; {{ date('Y') }} Gudang.in — Sistem Inventaris Internal</p>
    </div>
</body>
</html>
