{{--
    Sistem desain bersama Gudang.in.
    Dipakai oleh layouts/app.blade.php dan auth/login.blade.php.
    Seluruh warna adalah variabel CSS, jadi mode gelap cukup mengganti variabel
    (tidak perlu menyentuh class di setiap halaman).
--}}
<meta name="theme-color" content="#F3F4F0" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0F1311" media="(prefers-color-scheme: dark)">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400..800&family=Geist+Mono:wght@400..600&display=swap" rel="stylesheet">

{{-- Tema & status sidebar ditentukan sebelum paint supaya tidak berkedip --}}
<script>
    (function () {
        var d = document.documentElement;
        try {
            var t = localStorage.getItem('theme');
            if (t === 'dark' || (!t && matchMedia('(prefers-color-scheme: dark)').matches)) d.classList.add('dark');
            if (localStorage.getItem('sidebarCollapsed') === '1') d.setAttribute('data-side', 'collapsed');
        } catch (e) {}
        d.classList.add('js');
        // transisi baru aktif setelah frame pertama, supaya halaman tidak "bergerak" saat dibuka
        requestAnimationFrame(function () { requestAnimationFrame(function () { d.classList.add('is-ready'); }); });
    })();
</script>

<script src="https://cdn.tailwindcss.com"></script>
<script>
    (function () {
        var c = function (n) { return 'rgb(var(--' + n + ') / <alpha-value>)'; };
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Schibsted Grotesk"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        mono: ['"Geist Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        ink:   { 100: c('ink-100'), 700: c('ink-700'), 800: c('ink-800'), 900: c('ink'), 950: c('solid') },
                        moss:  { 50: c('moss-50'), 300: c('moss-300'), 400: c('moss-400'), 500: c('moss-500'), 600: c('moss-600'), 700: c('moss-700') },
                        amber: { 50: c('amber-50'), 300: c('amber-300'), 400: c('amber-400'), 500: c('amber-500'), 600: c('amber-600') },
                        rose:  { 50: c('rose-50'), 200: c('rose-200'), 400: c('rose-400'), 500: c('rose-500'), 600: c('rose-600'), 700: c('rose-700') },
                    },
                },
            },
        };
    })();
</script>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.14.1/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>

<style>
/* =====================================================================
   1. Token
   ===================================================================== */
:root {
    /* permukaan */
    --paper: 243 244 240;          /* latar halaman */
    --surface: 253 253 251;        /* kartu */
    --side: 236 238 233;           /* sidebar */
    --ink: 23 28 25;               /* teks utama (graphite berwarna hijau tipis) */
    --solid: 28 70 52;             /* tombol utama */
    --solid-hover: 22 57 42;
    --ink-100: 231 234 228;
    --ink-700: 52 60 55;
    --ink-800: 38 45 41;
    --line: 23 28 25;              /* dasar garis; opasitas diatur per pemakaian */

    /* pine */
    --moss-50: 230 239 233;  --moss-300: 134 178 152; --moss-400: 80 138 108;
    --moss-500: 48 106 78;   --moss-600: 34 84 61;    --moss-700: 26 64 47;
    /* kuningan (peringatan) */
    --amber-50: 247 240 222; --amber-300: 214 174 84; --amber-400: 196 150 48;
    --amber-500: 140 98 8;   --amber-600: 114 80 6;
    /* karat (bahaya) */
    --rose-50: 248 234 228;  --rose-200: 226 178 154; --rose-400: 192 108 68;
    --rose-500: 166 64 36;   --rose-600: 138 50 27;   --rose-700: 108 39 20;

    --ease: cubic-bezier(.22, 1, .36, 1);       /* easeOutExpo-ish: masuk cepat, berhenti lembut */
    --ease-io: cubic-bezier(.65, 0, .35, 1);
    --side-w: 17rem;
    --head-h: 3.75rem;
    --r-card: 18px;
    --r-ctl: 11px;
    color-scheme: light;
}
.dark {
    --paper: 15 19 17;
    --surface: 22 27 24;
    --side: 12 15 13;
    --ink: 232 236 229;
    --solid: 46 104 76;
    --solid-hover: 58 122 90;
    --ink-100: 214 220 211;
    --ink-700: 190 198 190;
    --ink-800: 200 208 200;

    --moss-50: 29 48 38;     --moss-300: 110 170 134; --moss-400: 90 156 120;
    --moss-500: 84 150 114;  --moss-600: 52 112 82;   --moss-700: 40 90 66;
    --amber-50: 52 42 20;    --amber-300: 220 180 92; --amber-400: 212 168 70;
    --amber-500: 226 184 96; --amber-600: 200 160 70;
    --rose-50: 58 32 25;     --rose-200: 140 80 62;   --rose-400: 210 120 90;
    --rose-500: 226 132 102; --rose-600: 190 98 70;   --rose-700: 232 160 134;
    color-scheme: dark;
}

/* =====================================================================
   2. Dasar
   ===================================================================== */
[x-cloak] { display: none !important; }
html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; scroll-padding-top: 5rem; }
body {
    background: rgb(var(--paper));
    color: rgb(var(--ink));
    font-family: 'Schibsted Grotesk', ui-sans-serif, system-ui, sans-serif;
    font-size: 15px;
    line-height: 1.55;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
    overflow-x: hidden;
}
::selection { background: rgb(var(--moss-500) / .25); }
:focus-visible { outline: 2px solid rgb(var(--moss-500)); outline-offset: 2px; border-radius: 6px; }
::-webkit-scrollbar { width: 10px; height: 10px; }
::-webkit-scrollbar-thumb { background: rgb(var(--ink) / .16); border-radius: 10px; border: 3px solid transparent; background-clip: padding-box; }
::-webkit-scrollbar-thumb:hover { background: rgb(var(--ink) / .3); background-clip: padding-box; border: 3px solid transparent; }

/* Angka: tabular supaya kolom rapi. Tidak lagi monospace. */
.sep { font-style: normal; margin: 0 -.09em; }
.stat-figure { font-variant-numeric: tabular-nums lining-nums; letter-spacing: -.02em; font-family: inherit !important; }
.font-mono { font-family: 'Geist Mono', ui-monospace, monospace; font-size: .92em; letter-spacing: -.01em; }

/* Bobot: satu keluarga variabel, bobotnya diturunkan dari bawaan Tailwind agar terasa tenang */
body .font-extrabold { font-weight: 640; }
body .font-bold { font-weight: 600; }
body .font-semibold { font-weight: 560; }
body .font-medium { font-weight: 500; }

/* Label kecil: tidak lagi huruf kapital berjarak; ukuran dinaikkan sedikit agar terbaca */
body .uppercase { text-transform: none; letter-spacing: normal; }
body .tracking-wide, body .tracking-wider { letter-spacing: normal; }
body .text-\[10px\] { font-size: 11.5px; line-height: 1.35; }
body .text-\[11px\] { font-size: 12px; line-height: 1.4; }
body .text-xs { font-size: 12.5px; }

/* Judul kartu */
body h2, body h3 { text-transform: none; letter-spacing: -.005em; }
body h2:not([class*="text-"]), body h3:not([class*="text-"]) { font-size: 15px; }
body h2.text-lg { font-size: 1.3rem; letter-spacing: -.02em; font-weight: 620; line-height: 1.2; }

/* =====================================================================
   3. Permukaan: kartu datar (garis tipis, tanpa bayangan seragam)
   ===================================================================== */
body .bg-white { background-color: rgb(var(--surface)); }
body .shadow-panel { box-shadow: none; }
body .rounded-xl { border-radius: var(--r-card); }
body .rounded-2xl { border-radius: 22px; }
body .rounded-lg { border-radius: var(--r-ctl); }
body .border-ink-900\/8 { border-color: rgb(var(--ink) / .09); }
body .bg-white.border { transition: border-color .25s var(--ease), background-color .25s var(--ease); }
body .bg-ink-900\/\[0\.03\] { background-color: rgb(var(--ink) / .035); }
body .shadow-2xl { box-shadow: 0 1px 0 rgb(var(--ink) / .06), 0 24px 48px -12px rgb(0 0 0 / .22), 0 8px 16px -8px rgb(0 0 0 / .12); }

/* kartu yang bisa diklik: respons halus, bukan lompatan */
a.bg-white.border { transition: border-color .25s var(--ease), background-color .25s var(--ease), transform .35s var(--ease); }
a.bg-white.border:hover { border-color: rgb(var(--moss-500) / .45); background-color: rgb(var(--surface)); }
a.bg-white.border:active { transform: scale(.992); }

/* =====================================================================
   4. Tombol
   ===================================================================== */
body .bg-ink-950.text-white,
body .bg-moss-600.text-white {
    background-color: rgb(var(--solid));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .14), 0 1px 2px rgb(0 0 0 / .12);
}
body .bg-ink-950.text-white:hover,
body .bg-moss-600.text-white:hover { background-color: rgb(var(--solid-hover)); }
body .bg-rose-500.text-white { box-shadow: inset 0 1px 0 rgb(255 255 255 / .14); }
body button, body a { -webkit-tap-highlight-color: transparent; }
body button:not(:disabled), body a.rounded-lg { transition: background-color .2s var(--ease), color .2s var(--ease), border-color .2s var(--ease), transform .25s var(--ease), box-shadow .25s var(--ease); }
body button:not(:disabled):active, body a.rounded-lg:active { transform: scale(.975); }
body .border-ink-900\/15 { border-color: rgb(var(--ink) / .15); }
body a.border.rounded-lg:hover, body button.border.rounded-lg:hover { border-color: rgb(var(--ink) / .28); }
body :is(a, button).rounded-lg:is(.text-xs, .text-sm) { min-height: 40px; font-size: 13.5px; }
@media (max-width: 639px) {
    .app-content form.flex-wrap > select { flex: 1 1 8rem; }
    table.is-stack td[data-role=actions] :is(a, button) { padding: 8px 2px; }
    .app-content > .flex.flex-col > :is(a, button).rounded-lg { justify-content: center; }
}
body .hover\:bg-ink-900\/5:hover { background-color: rgb(var(--ink) / .055); }
body .text-white.hover\:bg-ink-900:hover { background-color: rgb(var(--solid-hover)); }

/* =====================================================================
   5. Form
   ===================================================================== */
body input:not([type=checkbox]):not([type=radio]):not([type=hidden]):not([type=range]),
body select,
body textarea {
    background-color: rgb(var(--surface));
    border: 1px solid rgb(var(--ink) / .16);
    border-radius: var(--r-ctl);
    color: rgb(var(--ink));
    min-height: 42px;
    transition: border-color .2s var(--ease), box-shadow .25s var(--ease), background-color .2s var(--ease);
}
body textarea { min-height: 88px; }
body input:not([type=checkbox]):not([type=radio]):not([type=hidden]):hover,
body select:hover, body textarea:hover { border-color: rgb(var(--ink) / .28); }
body input:not([type=checkbox]):not([type=radio]):focus,
body select:focus, body textarea:focus {
    outline: none;
    border-color: rgb(var(--moss-500));
    box-shadow: 0 0 0 4px rgb(var(--moss-500) / .14);
}
body input::placeholder, body textarea::placeholder { color: rgb(var(--ink) / .38); }
body select:not([data-x]) { padding-right: 2.25rem; }
body select {
    appearance: none; -webkit-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23808a83' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right .7rem center; background-size: 1rem;
}
body input[type=checkbox] {
    appearance: none; -webkit-appearance: none;
    width: 18px; height: 18px; border-radius: 6px; flex: none;
    border: 1.5px solid rgb(var(--ink) / .3); background: rgb(var(--surface));
    display: inline-grid; place-content: center; cursor: pointer;
    transition: background-color .2s var(--ease), border-color .2s var(--ease), transform .2s var(--ease);
}
body input[type=checkbox]::after {
    content: ''; width: 10px; height: 10px; transform: scale(0);
    transition: transform .25s var(--ease);
    background: #fff;
    clip-path: polygon(14% 52%, 0 66%, 38% 100%, 100% 22%, 86% 8%, 38% 72%);
}
body input[type=checkbox]:checked { background: rgb(var(--moss-600)); border-color: rgb(var(--moss-600)); }
body input[type=checkbox]:checked::after { transform: scale(1); }
body input[type=checkbox]:active { transform: scale(.9); }
body label.block { color: rgb(var(--ink) / .72); }
@media (max-width: 639px) {
    /* 16px mencegah iOS memperbesar layar saat fokus */
    body input:not([type=checkbox]):not([type=radio]), body select, body textarea { font-size: 16px; }
}

/* =====================================================================
   6. Lencana status (pill)
   ===================================================================== */
body .rounded-full.bg-moss-50, body .rounded-full.bg-rose-50, body .rounded-full.bg-amber-400\/10, body .rounded-full.bg-ink-900\/5 {
    border-radius: 999px; padding-inline: .65rem; white-space: nowrap;
}
body .bg-amber-400\/10 { background-color: rgb(var(--amber-400) / .14); }
body .bg-sky-50 { background-color: rgb(14 116 144 / .1); }
body .text-sky-600 { color: rgb(14 116 144); }
.dark .text-moss-600, .dark .text-moss-700 { color: rgb(var(--moss-300)); }
.dark .group:hover .group-hover\:text-moss-600 { color: rgb(var(--moss-300)); }
.dark .text-rose-500, .dark .text-rose-700 { color: rgb(var(--rose-500)); }
.dark .text-sky-600 { color: rgb(125 211 252); }
.dark .bg-sky-50 { background-color: rgb(14 165 233 / .16); }
.dark .bg-ink-950.text-white { background-color: rgb(var(--solid)); }
.dark .hover\:text-moss-600:hover, .dark .hover\:text-moss-700:hover { color: rgb(var(--moss-300)); }

/* =====================================================================
   7. Tabel
   ===================================================================== */
body table { border-collapse: separate; border-spacing: 0; }
body thead th {
    text-transform: none; letter-spacing: normal;
    font-size: 12.5px; font-weight: 500; color: rgb(var(--ink) / .5);
    white-space: nowrap;
}
body thead tr { background: transparent !important; }
body tbody tr { transition: background-color .2s var(--ease); }
body tbody tr:hover { background-color: rgb(var(--ink) / .025); }
body tbody td { vertical-align: middle; }
body .overflow-x-auto { scrollbar-width: thin; }

/* Mobile: baris tabel menjadi daftar bertumpuk (label diisi otomatis oleh app.js) */
@media (max-width: 639px) {
    table.is-stack, table.is-stack tbody { display: block; width: 100%; min-width: 0 !important; }
    table.is-stack thead { display: none; }
    table.is-stack tr { display: block; padding: 14px 18px; border-bottom: 1px solid rgb(var(--ink) / .07); }
    table.is-stack tr:last-child { border-bottom: 0; }
    table.is-stack td {
        display: flex; align-items: baseline; justify-content: space-between; gap: 1rem;
        padding: 3px 0 !important; text-align: right !important; border: 0 !important;
    }
    table.is-stack td::before {
        content: attr(data-label); flex: none;
        font-size: 12.5px; color: rgb(var(--ink) / .45); text-align: left;
    }
    table.is-stack td:first-child { font-size: 15px; justify-content: flex-start; text-align: left !important; padding-bottom: 6px !important; }
    table.is-stack td:first-child::before { content: none; }
    table.is-stack td[data-role=actions] { justify-content: flex-end; padding-top: 10px !important; gap: .9rem; }
    table.is-stack td[data-role=actions]::before { content: none; }
    table.is-stack td[colspan] { display: block; text-align: center !important; padding: 28px 0 !important; }
    table.is-stack td[colspan]::before { content: none; }
    table.is-stack td p { text-align: right; }
    table.is-stack td:first-child p { text-align: left; }
    .overflow-x-auto:has(> table.is-stack) { overflow-x: visible; }
    .-mx-5:has(> table.is-stack), .-mx-6:has(> table.is-stack) { margin-inline: -1.25rem !important; padding-inline: 0 !important; }
}

/* =====================================================================
   8. Kerangka aplikasi
   ===================================================================== */
.app-side {
    position: fixed; inset: 0 auto 0 0; z-index: 40; width: var(--side-w);
    background: rgb(var(--side));
    display: flex; flex-direction: column;
    transform: translateX(-100%);
    view-transition-name: app-side;
}
.is-ready .app-side { transition: transform .5s var(--ease); }
.app-side.is-open { transform: none; }
.app-main { min-width: 0; }
@media (min-width: 1024px) {
    .app-side { transform: none; border-right: 1px solid rgb(var(--ink) / .07); }
    .app-main { margin-left: var(--side-w); }
    .is-ready .app-main { transition: margin-left .5s var(--ease); }
    html[data-side=collapsed] .app-side { transform: translateX(-100%); }
    html[data-side=collapsed] .app-main { margin-left: 0; }
}
.scrim { position: fixed; inset: 0; z-index: 35; background: rgb(8 12 10 / .42); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); }

.brand { display: flex; align-items: center; gap: .7rem; height: var(--head-h); padding: 0 1.25rem 0 1.15rem; flex: none; }
.brand-name { font-weight: 640; font-size: 1.0625rem; letter-spacing: -.02em; }

.nav-scroll { flex: 1; overflow-y: auto; padding: .35rem .75rem 1rem; position: relative; overscroll-behavior: contain; }
.nav-group + .nav-group { margin-top: 1.1rem; }
.nav-heading { padding: 0 .75rem .35rem; font-size: 12px; font-weight: 500; color: rgb(var(--ink) / .42); }
.nav-link {
    position: relative; z-index: 1;
    display: flex; align-items: center; gap: .7rem;
    height: 38px; padding: 0 .75rem; border-radius: 10px;
    font-size: 14px; font-weight: 500; color: rgb(var(--ink) / .62);
    transition: color .2s var(--ease), background-color .2s var(--ease);
}
.nav-link svg { width: 18px; height: 18px; flex: none; stroke-width: 1.6; opacity: .85; transition: transform .35s var(--ease), opacity .2s; }
.nav-link:hover { color: rgb(var(--ink)); background: rgb(var(--ink) / .045); }
.nav-link:hover svg { transform: translateX(1px) scale(1.06); opacity: 1; }
.nav-link.is-active { color: rgb(var(--ink)); font-weight: 580; }
.nav-link.is-active svg { color: rgb(var(--moss-500)); opacity: 1; }
.nav-link .count { margin-left: auto; font-size: 11.5px; font-weight: 600; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 99px; display: grid; place-items: center; background: rgb(var(--amber-400) / .2); color: rgb(var(--amber-500)); }
/* Penanda aktif: satu elemen yang meluncur dari item sebelumnya ke item sekarang */
.nav-indicator {
    position: absolute; left: .75rem; right: .75rem; top: 0; height: 38px; z-index: 0; border-radius: 10px;
    background: rgb(var(--surface)); box-shadow: 0 0 0 1px rgb(var(--ink) / .07), 0 1px 2px rgb(var(--ink) / .06);
    opacity: 0; pointer-events: none;
}
.nav-indicator.is-on { opacity: 1; }
.nav-indicator::before { content: ''; position: absolute; left: 0; top: 11px; bottom: 11px; width: 3px; border-radius: 0 3px 3px 0; background: rgb(var(--moss-500)); }
.nav-indicator.is-animating { transition: transform .55s var(--ease), opacity .3s; }

.side-user { margin: .5rem .75rem .9rem; padding: .6rem; border-radius: 14px; display: flex; align-items: center; gap: .65rem; }
.avatar { width: 34px; height: 34px; border-radius: 10px; background: rgb(var(--moss-600)); color: #fff; display: grid; place-items: center; font-weight: 600; font-size: 14px; flex: none; }

/* Header */
.app-head {
    position: sticky; top: 0; z-index: 30; height: var(--head-h);
    display: flex; align-items: center; gap: .5rem; padding: 0 1rem;
    background: rgb(var(--paper) / .78);
    backdrop-filter: saturate(1.4) blur(14px); -webkit-backdrop-filter: saturate(1.4) blur(14px);
    border-bottom: 1px solid transparent;
    transition: border-color .3s var(--ease);
    view-transition-name: app-head;
}
.app-head.is-scrolled { border-bottom-color: rgb(var(--ink) / .08); }
@media (min-width: 1024px) { .app-head { padding: 0 2.5rem; } }
.head-title {
    font-weight: 600; font-size: 15px; letter-spacing: -.01em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    opacity: 0; transform: translateY(6px); transition: opacity .3s var(--ease), transform .4s var(--ease);
}
.app-head.is-scrolled .head-title { opacity: 1; transform: none; }
.icon-btn {
    width: 38px; height: 38px; border-radius: 11px; display: grid; place-items: center; flex: none;
    color: rgb(var(--ink) / .7); border: 1px solid transparent; position: relative;
}
.icon-btn:hover { background: rgb(var(--ink) / .06); color: rgb(var(--ink)); }
.side-show { display: none; }
@media (min-width: 1024px) { html[data-side=collapsed] .side-show { display: grid; } }
.icon-btn svg { width: 19px; height: 19px; stroke-width: 1.7; }
.btn-primary {
    display: inline-flex; align-items: center; gap: .45rem; height: 38px; padding: 0 .9rem; border-radius: 11px;
    font-size: 13.5px; font-weight: 560; color: #fff; background: rgb(var(--solid));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .14), 0 1px 2px rgb(0 0 0 / .12);
}
.btn-primary:hover { background: rgb(var(--solid-hover)); }
.btn-primary svg { width: 16px; height: 16px; stroke-width: 2; }
.btn-quiet {
    display: inline-flex; align-items: center; gap: .45rem; height: 38px; padding: 0 .85rem; border-radius: 11px;
    font-size: 13.5px; font-weight: 540; color: rgb(var(--ink) / .8); border: 1px solid rgb(var(--ink) / .13);
}
.btn-quiet:hover { background: rgb(var(--ink) / .05); border-color: rgb(var(--ink) / .24); }
.btn-quiet svg { width: 16px; height: 16px; stroke-width: 1.8; }

.theme-toggle .icon-sun, .theme-toggle .icon-moon { position: absolute; transition: transform .6s var(--ease), opacity .35s var(--ease); }
.theme-toggle .icon-sun { opacity: 0; transform: rotate(-70deg) scale(.5); }
.dark .theme-toggle .icon-sun { opacity: 1; transform: none; }
.dark .theme-toggle .icon-moon { opacity: 0; transform: rotate(70deg) scale(.5); }

.bell-dot { position: absolute; top: 2px; right: 0; min-width: 16px; height: 16px; padding: 0 4px; border-radius: 99px; background: rgb(var(--rose-500)); color: #fff; font-size: 10px; font-weight: 700; display: grid; place-items: center; box-shadow: 0 0 0 2px rgb(var(--paper)); }
.dark .bell-dot { color: #1a0d09; }

.popover {
    position: absolute; right: 0; top: calc(100% + .5rem); width: min(22rem, calc(100vw - 2rem)); z-index: 50;
    background: rgb(var(--surface)); border: 1px solid rgb(var(--ink) / .1); border-radius: 16px; overflow: hidden;
    box-shadow: 0 1px 0 rgb(var(--ink) / .04), 0 24px 48px -16px rgb(0 0 0 / .25), 0 8px 16px -10px rgb(0 0 0 / .14);
    transform-origin: top right;
}

/* Judul halaman */
.page-head { margin-bottom: 1.75rem; }
.page-title { font-size: clamp(1.75rem, 1.25rem + 1.7vw, 2.6rem); font-weight: 620; letter-spacing: -.035em; line-height: 1.08; text-wrap: balance; }
.page-sub { margin-top: .5rem; color: rgb(var(--ink) / .55); font-size: 15px; max-width: 46ch; text-wrap: pretty; }
@media (min-width: 1024px) { .page-head { margin-bottom: 2.25rem; } }

/* Notifikasi flash */
.flash { display: flex; align-items: flex-start; gap: .7rem; padding: .85rem 1rem; border-radius: 14px; font-size: 14px; margin-bottom: 1.25rem; overflow: hidden; }
.flash::before { content: ''; width: 8px; height: 8px; border-radius: 99px; margin-top: .45rem; flex: none; background: currentColor; }
.flash-ok { background: rgb(var(--moss-50)); color: rgb(var(--moss-700)); }
.flash-err { background: rgb(var(--rose-50)); color: rgb(var(--rose-700)); }
.dark .flash-ok { color: rgb(var(--moss-300)); }

/* Bilah bawah (mobile) */
.dock {
    position: fixed; left: .75rem; right: .75rem; bottom: calc(.6rem + env(safe-area-inset-bottom)); z-index: 30;
    display: grid; grid-template-columns: repeat(5, 1fr); align-items: center;
    height: 62px; padding: 0 .35rem; border-radius: 22px;
    background: rgb(var(--surface) / .86);
    backdrop-filter: saturate(1.5) blur(18px); -webkit-backdrop-filter: saturate(1.5) blur(18px);
    border: 1px solid rgb(var(--ink) / .09);
    box-shadow: 0 12px 32px -14px rgb(0 0 0 / .35);
    view-transition-name: app-dock;
}
@media (min-width: 1024px) { .dock { display: none; } }
.dock a, .dock button { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px; height: 100%; font-size: 11px; font-weight: 520; color: rgb(var(--ink) / .5); position: relative; }
.dock svg { width: 21px; height: 21px; stroke-width: 1.7; transition: transform .4s var(--ease); }
.dock .is-active { color: rgb(var(--moss-600)); }
.dark .dock .is-active { color: rgb(var(--moss-300)); }
.dock .is-active svg { transform: translateY(-1px) scale(1.08); }
.dock .is-active::after { content: ''; position: absolute; top: 5px; width: 18px; height: 3px; border-radius: 3px; background: currentColor; animation: dock-pip .5s var(--ease) both; }
.dock .dock-scan { color: #fff; }
.dock .dock-scan span.disc { width: 46px; height: 46px; border-radius: 16px; background: rgb(var(--solid)); display: grid; place-items: center; margin-top: -22px; box-shadow: 0 10px 20px -8px rgb(var(--solid) / .7), inset 0 1px 0 rgb(255 255 255 / .16); border: 3px solid rgb(var(--paper)); transition: transform .35s var(--ease); }
.dock .dock-scan:active span.disc { transform: scale(.92); }
.dock .dock-scan.is-active::after { content: none; }
.dock .dock-scan small { color: rgb(var(--ink) / .5); font-size: 11px; font-weight: 520; margin-top: 1px; }
@keyframes dock-pip { from { transform: scaleX(0); opacity: 0; } to { transform: none; opacity: 1; } }

body .is-busy { opacity: .6; pointer-events: none; }

/* Progres navigasi */
.nav-progress { position: fixed; top: 0; left: 0; right: 0; height: 2px; z-index: 100; pointer-events: none; transform-origin: 0 50%; transform: scaleX(0); opacity: 0; background: rgb(var(--moss-500)); }
.nav-progress.is-run { opacity: 1; transform: scaleX(.72); transition: transform 8s cubic-bezier(.1, .6, .2, 1); }
.nav-progress.is-done { opacity: 0; transform: scaleX(1); transition: transform .25s var(--ease), opacity .45s .2s; }

/* =====================================================================
   9. Gerak
   Satu pola saja: konten masuk berurutan sekali saat halaman dibuka.
   ===================================================================== */
@keyframes rise { from { opacity: 0; transform: translate3d(0, 10px, 0); } to { opacity: 1; transform: none; } }
@keyframes settle { from { opacity: 0; transform: translate3d(0, 6px, 0) scale(.992); } to { opacity: 1; transform: none; } }
@keyframes grow-x { from { transform: scaleX(0); } to { transform: scaleX(1); } }
@keyframes pop { from { opacity: 0; transform: scale(.96) translateY(6px); } to { opacity: 1; transform: none; } }

.app-content > * { animation: rise .7s var(--ease) backwards; }
.app-content > :nth-child(1) { animation-delay: 0ms; }
.app-content > :nth-child(2) { animation-delay: 70ms; }
.app-content > :nth-child(3) { animation-delay: 130ms; }
.app-content > :nth-child(4) { animation-delay: 185ms; }
.app-content > :nth-child(5) { animation-delay: 235ms; }
.app-content > :nth-child(n+6) { animation-delay: 280ms; }
/* grid kartu: anak-anaknya yang bergerak, bukan grid-nya */
.app-content > .grid { animation: none; }
.app-content > .grid > * { animation: rise .7s var(--ease) backwards; animation-delay: 110ms; }
.app-content > .grid > :nth-child(2) { animation-delay: 150ms; }
.app-content > .grid > :nth-child(3) { animation-delay: 190ms; }
.app-content > .grid > :nth-child(4) { animation-delay: 230ms; }
.app-content > .grid > :nth-child(5) { animation-delay: 265ms; }
.app-content > .grid > :nth-child(6) { animation-delay: 300ms; }
.app-content > .grid > :nth-child(7) { animation-delay: 330ms; }
.app-content > .grid > :nth-child(8) { animation-delay: 360ms; }
.app-content > .grid > :nth-child(9) { animation-delay: 385ms; }
.app-content > .grid > :nth-child(n+10) { animation-delay: 410ms; }

.bar-grow { transform-origin: 0 50%; animation: grow-x 1.1s var(--ease) .35s backwards; }

/* Dialog: kartu muncul dengan skala halus; latar memudar lewat x-transition */
.fixed.inset-0.z-50 > .bg-white { animation: pop .5s var(--ease) both; }

/* Transisi antar halaman (browser yang mendukung): kerangka diam, isi bertukar lembut */
@view-transition { navigation: auto; }
::view-transition-group(app-side), ::view-transition-group(app-head), ::view-transition-group(app-dock) { animation: none; }
::view-transition-old(app-side), ::view-transition-new(app-side),
::view-transition-old(app-head), ::view-transition-new(app-head),
::view-transition-old(app-dock), ::view-transition-new(app-dock) { animation: none; mix-blend-mode: normal; }
::view-transition-old(root) { animation: vt-out .16s ease both; }
::view-transition-new(root) { animation: vt-in .28s var(--ease) both; }
@keyframes vt-out { to { opacity: 0; } }
@keyframes vt-in { from { opacity: 0; } }

/* Ganti tema: semua warna berpindah halus */
html.theme-anim *, html.theme-anim *::before, html.theme-anim *::after {
    transition: background-color .45s var(--ease), border-color .45s var(--ease), color .45s var(--ease), fill .45s, stroke .45s !important;
}

@media (prefers-reduced-motion: reduce) {
    html { scroll-behavior: auto; }
    *, *::before, *::after { animation-duration: .001ms !important; animation-delay: 0ms !important; transition-duration: .001ms !important; }
    @view-transition { navigation: none; }
}

/* =====================================================================
   10. Dasbor
   ===================================================================== */
.ledger { display: grid; border-radius: 22px; overflow: hidden; background: rgb(var(--surface)); border: 1px solid rgb(var(--ink) / .09); }
.ledger-lead { position: relative; padding: 1.5rem 1.5rem 1.6rem; color: #F2F6F1; background: rgb(var(--moss-700)); isolation: isolate; overflow: hidden; }
.dark .ledger-lead { background: rgb(34 78 58); }
/* motif rak gudang: garis horizontal tipis seperti rak berbaris */
.ledger-lead::before { content: ''; position: absolute; inset: 0; z-index: -1; opacity: .5;
    background: repeating-linear-gradient(180deg, transparent 0 27px, rgb(255 255 255 / .06) 27px 28px);
    mask-image: linear-gradient(100deg, transparent 15%, #000 85%); -webkit-mask-image: linear-gradient(100deg, transparent 15%, #000 85%); }
.ledger-lead .lead-label { color: rgb(242 246 241 / .72); font-size: 14px; }
body .ledger-lead .lead-fig, body .ledger-cell .cell-fig { font-variant-numeric: lining-nums proportional-nums; }
.ledger-lead .lead-fig { font-size: clamp(3.5rem, 2.4rem + 5vw, 5.75rem); font-weight: 600; line-height: .95; letter-spacing: -.05em; margin-top: .6rem; }
.ledger-lead .lead-note { margin-top: 1.1rem; font-size: 14px; color: rgb(242 246 241 / .82); display: flex; align-items: center; gap: .55rem; }
.ledger-lead .lead-note a { text-decoration: underline; text-underline-offset: 3px; text-decoration-color: rgb(242 246 241 / .4); }
.ledger-cells { display: grid; grid-template-columns: repeat(2, 1fr); }
.ledger-cell { padding: 1.05rem 1.25rem 1.15rem; border-top: 1px solid rgb(var(--ink) / .08); position: relative; }
.ledger-cell:nth-child(odd) { border-right: 1px solid rgb(var(--ink) / .08); }
.ledger-cell .cell-label { font-size: 13px; color: rgb(var(--ink) / .52); display: flex; align-items: center; gap: .45rem; }
.ledger-cell .cell-fig { font-size: 1.65rem; font-weight: 600; letter-spacing: -.03em; line-height: 1.15; margin-top: .3rem; }
.ledger-cell .cell-note { font-size: 12.5px; color: rgb(var(--ink) / .45); margin-top: .15rem; }
.ledger-cell .cell-note a { color: rgb(var(--amber-500)); font-weight: 560; }
.ledger-cell .cell-note a:hover { text-decoration: underline; }
.pip { width: 7px; height: 7px; border-radius: 99px; background: rgb(var(--amber-400)); flex: none; }
.pip.is-live { box-shadow: 0 0 0 0 rgb(var(--amber-400) / .5); animation: pip 2.4s ease-out infinite; }
@keyframes pip { 0% { box-shadow: 0 0 0 0 rgb(var(--amber-400) / .45); } 70%, 100% { box-shadow: 0 0 0 8px rgb(var(--amber-400) / 0); } }
@media (min-width: 640px) {
    .ledger-cell .cell-fig { font-size: 1.9rem; }
}
@media (min-width: 1024px) {
    .ledger { grid-template-columns: minmax(300px, 4fr) 8fr; }
    .ledger-lead { padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; }
    .ledger-cells { grid-template-columns: repeat(3, 1fr); }
    .ledger-cell { padding: 1.4rem 1.5rem; }
    .ledger-cell { border-top: 0; border-right: 0 !important; border-left: 1px solid rgb(var(--ink) / .08); }
    .ledger-cell:nth-child(n+4) { border-top: 1px solid rgb(var(--ink) / .08); }
    .ledger-cell:nth-child(3n+1) { border-left: 1px solid rgb(var(--ink) / .08); }
}

.meter { height: 4px; border-radius: 99px; background: rgb(var(--ink) / .08); overflow: hidden; }
.meter > i { display: block; height: 100%; border-radius: inherit; background: rgb(var(--moss-500)); transform-origin: 0 50%; animation: grow-x 1s var(--ease) .3s backwards; }
.meter.is-low > i { background: rgb(var(--amber-400)); }
.meter.is-out > i { background: rgb(var(--rose-500)); }

.row-link { display: flex; align-items: center; justify-content: space-between; gap: .75rem; padding: .7rem .75rem; margin-inline: -.75rem; border-radius: 12px; transition: background-color .2s var(--ease); }
.row-link:hover { background: rgb(var(--ink) / .04); }

.chart-box { position: relative; width: 100%; }

/* =====================================================================
   11. Responsif tambahan
   ===================================================================== */
/* Dialog: tidak pernah lebih tinggi dari layar; di HP tampil sebagai lembar dari bawah */
.fixed.inset-0.z-50 > .bg-white { max-height: calc(100dvh - 2rem); overflow-y: auto; overscroll-behavior: contain; }
@media (max-width: 639px) {
    .fixed.inset-0.z-50.grid { padding: 0 !important; place-items: end center; }
    .fixed.inset-0.z-50.grid > .bg-white {
        max-width: none !important; border-bottom-left-radius: 0; border-bottom-right-radius: 0;
        max-height: 92dvh; padding-bottom: env(safe-area-inset-bottom);
        animation: sheet-up .4s var(--ease) both;
    }
}
@keyframes sheet-up { from { transform: translateY(40px); opacity: 0; } to { transform: none; opacity: 1; } }

/* Baris tombol aksi pada form: bertumpuk & selebar layar di HP kecil */
@media (max-width: 479px) {
    .app-content form > .flex.items-center.gap-2.border-t { flex-direction: column-reverse; align-items: stretch; }
    .app-content form > .flex.items-center.gap-2.border-t > :is(a, button) { text-align: center; justify-content: center; min-height: 44px; }
}
/* Kontrol form & filter: target sentuh nyaman */
@media (max-width: 639px) {
    body input:not([type=checkbox]):not([type=radio]):not([type=hidden]), body select { min-height: 46px; }
    .app-content form.flex-wrap > :is(input, select) { min-width: 0 !important; flex: 1 1 100%; }
    .app-content form.flex-wrap > a { flex: 1 1 auto; justify-content: center; }
}
/* Tidak ada elemen yang mendorong lebar halaman */
img, svg, video, canvas { max-width: 100%; }
.app-content { overflow-wrap: anywhere; }
.app-content table { overflow-wrap: normal; }

/* =====================================================================
   12. Sidebar & navbar v2
   ===================================================================== */
:root { --nav-h: 40px; --side-w: 17.5rem; --head-h: 4rem; }

/* ---------- Sidebar ---------- */
.app-side {
    background:
        radial-gradient(130% 55% at 0% 0%, rgb(var(--moss-500) / .13), transparent 62%),
        linear-gradient(180deg, rgb(var(--side)), rgb(var(--side)) 70%, rgb(var(--side) / .96));
    border-right: 1px solid rgb(var(--ink) / .07);
}
.brand { height: 4.25rem; padding: 0 1rem 0 1.1rem; }
.brand-link { display: flex; align-items: center; gap: .7rem; min-width: 0; }
.brand-logo { display: grid; flex: none; filter: drop-shadow(0 6px 10px rgb(var(--moss-700) / .35)); transition: transform .5s var(--ease); }
.brand-logo svg { border-radius: 9px; box-shadow: inset 0 1px 0 rgb(255 255 255 / .22); }
.brand-link:hover .brand-logo { transform: rotate(-6deg) scale(1.05); }
.brand-text { display: grid; line-height: 1.15; min-width: 0; }
.brand-name { font-size: 1.0625rem; font-weight: 650; letter-spacing: -.025em; }
.brand-sub { font-size: 11.5px; color: rgb(var(--ink) / .45); letter-spacing: 0; }

.nav-scroll { padding: .25rem .8rem 1rem; }
.nav-group + .nav-group { margin-top: 1.25rem; }
.nav-heading {
    display: flex; align-items: center; gap: .6rem; padding: 0 .75rem .45rem;
    font-size: 11.5px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase !important; color: rgb(var(--ink) / .38);
}
body .nav-heading { text-transform: uppercase; letter-spacing: .06em; }
.nav-heading::after { content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, rgb(var(--ink) / .1), transparent); }

.nav-link { height: var(--nav-h); border-radius: 12px; gap: .75rem; font-size: 14px; color: rgb(var(--ink) / .6); }
.nav-link svg { width: 19px; height: 19px; stroke-width: 1.6; }
.nav-link:hover { background: rgb(var(--ink) / .05); color: rgb(var(--ink)); }
.nav-link:hover svg { transform: translateX(1px) scale(1.08); }
.nav-link.is-active { color: rgb(var(--ink)); font-weight: 600; }
.nav-link.is-active svg { color: rgb(var(--moss-500)); stroke-width: 1.9; }
.dark .nav-link.is-active svg { color: rgb(var(--moss-300)); }
.nav-link .count { background: rgb(var(--amber-400) / .22); box-shadow: inset 0 0 0 1px rgb(var(--amber-400) / .25); }

/* penanda aktif: kartu terangkat dengan semburat hijau dan batang kiri bercahaya */
.nav-indicator {
    left: .8rem; right: .8rem; height: var(--nav-h); border-radius: 12px;
    background: linear-gradient(100deg, rgb(var(--moss-500) / .10), transparent 70%), rgb(var(--surface));
    box-shadow: 0 0 0 1px rgb(var(--ink) / .07), 0 1px 2px rgb(var(--ink) / .06), 0 8px 18px -10px rgb(var(--moss-600) / .35);
}
.nav-indicator::before { left: 0; top: 10px; bottom: 10px; width: 3px; background: linear-gradient(180deg, rgb(var(--moss-400)), rgb(var(--moss-600))); box-shadow: 0 0 10px rgb(var(--moss-500) / .6); }

/* kartu pengguna */
.side-user {
    margin: .4rem .8rem .9rem; padding: .6rem .6rem .6rem .65rem; border-radius: 16px;
    background: rgb(var(--surface)); border: 1px solid rgb(var(--ink) / .08);
    box-shadow: 0 1px 2px rgb(var(--ink) / .04);
}
.avatar { background: linear-gradient(135deg, rgb(var(--moss-400)), rgb(var(--moss-700))); box-shadow: inset 0 1px 0 rgb(255 255 255 / .25); }

/* jarak antar menu */
.nav-group { display: flex; flex-direction: column; gap: 6px; }
.nav-group + .nav-group { margin-top: 1.5rem; }
.nav-heading { margin-bottom: .1rem; }

/* ---------- Navbar ---------- */
.app-head {
    height: var(--head-h); gap: .6rem; padding: 0 1rem;
    background: rgb(var(--paper) / .72);
    backdrop-filter: saturate(1.6) blur(18px); -webkit-backdrop-filter: saturate(1.6) blur(18px);
}
.app-head::after { content: ''; position: absolute; left: 0; right: 0; bottom: -1px; height: 1px; background: linear-gradient(90deg, transparent, rgb(var(--ink) / .1) 15%, rgb(var(--ink) / .1) 85%, transparent); opacity: 0; transition: opacity .3s var(--ease); pointer-events: none; }
.app-head.is-scrolled { border-bottom-color: transparent; }
.app-head.is-scrolled::after { opacity: 1; }
@media (min-width: 1024px) { .app-head { padding: 0 2.5rem; } }

.crumb { display: none; align-items: center; gap: .35rem; min-width: 0; font-size: 14px; white-space: nowrap; }
@media (min-width: 1024px) { .crumb { display: flex; } }
.crumb-root { color: rgb(var(--ink) / .45); }
.crumb-sep { width: 14px; height: 14px; stroke-width: 2; color: rgb(var(--ink) / .3); flex: none; }
.crumb-cur { font-weight: 600; overflow: hidden; text-overflow: ellipsis; }

/* pencarian: pil kaca, membesar saat fokus */
.hsearch {
    display: none; align-items: center; gap: .55rem; height: 40px; padding: 0 .55rem 0 .85rem; margin-left: auto;
    width: clamp(14rem, 26vw, 22rem); border-radius: 999px;
    background: rgb(var(--surface) / .7); border: 1px solid rgb(var(--ink) / .1);
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .35), 0 1px 2px rgb(var(--ink) / .04);
    transition: width .45s var(--ease), border-color .2s var(--ease), box-shadow .3s var(--ease), background-color .2s;
}
@media (min-width: 768px) { .hsearch { display: flex; } }
.hsearch:hover { border-color: rgb(var(--ink) / .2); }
.hsearch:focus-within { width: clamp(16rem, 36vw, 30rem); border-color: rgb(var(--moss-500)); background: rgb(var(--surface)); box-shadow: 0 0 0 4px rgb(var(--moss-500) / .14); }
.hs-ico { width: 17px; height: 17px; flex: none; stroke-width: 1.9; color: rgb(var(--ink) / .45); }
body .hsearch input[type=search] {
    flex: 1; min-width: 0; height: 100%; min-height: 0 !important; padding: 0 !important; border: 0 !important; background: transparent !important;
    box-shadow: none !important; border-radius: 0 !important; font-size: 14px; -webkit-appearance: none; appearance: none;
}
.hsearch input::-webkit-search-cancel-button { -webkit-appearance: none; }
.hs-kbd {
    font: 500 11.5px/1 'Geist Mono', ui-monospace, monospace; color: rgb(var(--ink) / .45);
    padding: .3rem .45rem; border-radius: 7px; background: rgb(var(--ink) / .06); border: 1px solid rgb(var(--ink) / .08);
    transition: opacity .2s;
}
.hsearch:focus-within .hs-kbd { opacity: 0; }

.head-actions { display: flex; align-items: center; gap: .4rem; margin-left: auto; flex: none; }
@media (min-width: 768px) { .head-actions { margin-left: 0; } }
.head-sep { width: 1px; height: 22px; background: rgb(var(--ink) / .12); margin: 0 .3rem; }
.icon-btn { border-radius: 12px; }
.icon-btn:hover { background: rgb(var(--ink) / .07); }
.btn-primary {
    height: 40px; padding: 0 1rem; border-radius: 12px;
    background: linear-gradient(180deg, rgb(var(--moss-500)), rgb(var(--solid)));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .2), 0 6px 14px -6px rgb(var(--moss-600) / .6);
}
.btn-primary:hover { background: linear-gradient(180deg, rgb(var(--moss-400)), rgb(var(--solid-hover))); transform: translateY(-1px); }
.btn-primary:active { transform: scale(.97); }
.btn-quiet { height: 40px; border-radius: 12px; background: rgb(var(--surface) / .6); }

.avatar-btn {
    width: 38px; height: 38px; border-radius: 999px; display: grid; place-items: center; margin-left: .15rem;
    font-weight: 620; font-size: 14px; color: #fff; flex: none;
    background: linear-gradient(135deg, rgb(var(--moss-400)), rgb(var(--moss-700)));
    box-shadow: inset 0 1px 0 rgb(255 255 255 / .25), 0 0 0 2px rgb(var(--paper)), 0 0 0 3.5px rgb(var(--moss-500) / .35);
    transition: transform .3s var(--ease), box-shadow .3s var(--ease);
}
.avatar-btn:hover { transform: scale(1.06); }

.popover { border-radius: 18px; }
.popover-sm { width: min(16.5rem, calc(100vw - 2rem)); }
.acct-head { display: flex; align-items: center; gap: .7rem; padding: .9rem 1rem; border-bottom: 1px solid rgb(var(--ink) / .08); background: linear-gradient(180deg, rgb(var(--moss-500) / .07), transparent); }
.menu-item { display: flex; align-items: center; gap: .7rem; width: 100%; height: 40px; padding: 0 .65rem; border-radius: 10px; font-size: 14px; font-weight: 500; text-align: left; color: rgb(var(--ink) / .8); }
.menu-item:hover { background: rgb(var(--ink) / .06); color: rgb(var(--ink)); }
.menu-item .mi-ico { width: 28px; height: 28px; border-radius: 8px; display: grid; place-items: center; background: rgb(var(--ink) / .05); flex: none; }
.menu-item svg { width: 16px; height: 16px; stroke-width: 1.8; }
.menu-item.is-danger { color: rgb(var(--rose-500)); }
.menu-item.is-danger .mi-ico { background: rgb(var(--rose-500) / .12); }
.menu-item.is-danger:hover { background: rgb(var(--rose-500) / .1); color: rgb(var(--rose-600)); }
.dark .menu-item.is-danger:hover { color: rgb(var(--rose-700)); }

/* HP: header ringkas, aksi tidak menumpuk */
@media (max-width: 479px) {
    .app-head { padding: 0 .75rem; gap: .35rem; }
    .head-actions { gap: .15rem; }
    .btn-primary { padding: 0; width: 40px; justify-content: center; }
}

/* =====================================================================
   13. Flat premium: tanpa bayangan & gradasi, hanya garis tipis dan warna solid
   ===================================================================== */
/* Sidebar */
.app-side { background: rgb(var(--side)); }
.brand-logo { filter: none; }
.brand-logo svg { box-shadow: none; }
.nav-indicator {
    background: rgb(var(--surface)); box-shadow: none; border: 1px solid rgb(var(--ink) / .09);
}
.dark .nav-indicator { background: rgb(var(--ink) / .06); border-color: rgb(var(--ink) / .08); }
.nav-indicator::before { top: 11px; bottom: 11px; left: -1px; width: 3px; background: rgb(var(--moss-500)); box-shadow: none; border-radius: 0 3px 3px 0; }
.nav-link .count { box-shadow: none; }
.nav-heading::after { background: rgb(var(--ink) / .08); }
.side-user { box-shadow: none; background: transparent; border: 1px solid rgb(var(--ink) / .1); }
.avatar, .avatar-btn { background: rgb(var(--moss-600)); box-shadow: none; }
.avatar-btn { border: 2px solid rgb(var(--paper)); outline: 1.5px solid rgb(var(--moss-500) / .45); outline-offset: 0; }
.avatar-btn:hover { transform: none; outline-color: rgb(var(--moss-500)); }

/* Navbar */
.app-head { background: rgb(var(--paper) / .9); }
.app-head::after { display: none; }
.app-head.is-scrolled { border-bottom-color: rgb(var(--ink) / .08); }
.hsearch { background: rgb(var(--surface)); box-shadow: none; }
.hsearch:focus-within { box-shadow: none; }
.btn-primary, body .bg-ink-950.text-white, body .bg-moss-600.text-white, body .bg-rose-500.text-white {
    background: rgb(var(--solid)); box-shadow: none;
}
.btn-primary:hover, body .bg-ink-950.text-white:hover, body .bg-moss-600.text-white:hover { background: rgb(var(--solid-hover)); transform: none; }
.btn-quiet { background: transparent; }
.acct-head { background: transparent; }

/* Popover, dialog, dock, dropdown select */
.popover, .sel-panel, .dock { box-shadow: none; border: 1px solid rgb(var(--ink) / .12); }
body .shadow-2xl { box-shadow: none; border: 1px solid rgb(var(--ink) / .12); }
.dock .dock-scan span.disc { box-shadow: none; }
select.sel-open { box-shadow: 0 0 0 1px rgb(var(--moss-500)) !important; }
/* fokus form: garis solid tipis, tanpa pendar */
body input:not([type=checkbox]):not([type=radio]):focus, body select:focus, body textarea:focus { box-shadow: 0 0 0 1px rgb(var(--moss-500)); }
.hsearch:focus-within { box-shadow: 0 0 0 1px rgb(var(--moss-500)); }
.ledger-lead::before { display: none; }
</style>
