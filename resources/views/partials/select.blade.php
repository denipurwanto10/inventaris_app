{{--
    Select kustom responsif untuk SEMUA <select> di aplikasi.

    Cara kerja:
    - <select> asli tetap ada di halaman (tetap ikut form, tetap memicu onchange / x-model Alpine,
      dan tetap menampilkan nilai terpilihnya). Yang diganti hanya popup bawaan browser.
    - Layar lebar  : daftar muncul tepat di bawah (atau di atas bila ruang kurang) select.
    - Layar kecil  : daftar muncul sebagai bottom sheet dengan target sentuh besar.
    - Select dinamis (x-for / modal) dikenali otomatis lewat MutationObserver.
    - Opt-out: tambahkan atribut data-native pada select yang ingin tetap memakai popup bawaan.
--}}
<style>
.sel-panel, .sel-sheet { font-family: 'Schibsted Grotesk', ui-sans-serif, system-ui, sans-serif; }
.sel-scrim {
    position: fixed; inset: 0; z-index: 90; background: rgb(8 12 10 / .42);
    backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px);
    animation: sel-fade .25s var(--ease) both;
}
.sel-panel {
    position: fixed; z-index: 91; display: flex; flex-direction: column; overflow: hidden;
    background: rgb(var(--surface)); color: rgb(var(--ink));
    border: 1px solid rgb(var(--ink) / .1); border-radius: 14px;
    box-shadow: 0 1px 0 rgb(var(--ink) / .04), 0 24px 48px -16px rgb(0 0 0 / .28), 0 8px 16px -10px rgb(0 0 0 / .16);
    transform-origin: var(--sel-origin, top left);
    animation: sel-pop .22s var(--ease) both;
}
.sel-panel.is-sheet {
    left: 0; right: 0; bottom: 0; top: auto; width: auto; max-height: min(78dvh, 34rem);
    border-radius: 22px 22px 0 0; border-width: 1px 0 0;
    padding-bottom: env(safe-area-inset-bottom);
    transform-origin: 50% 100%;
    animation: sel-sheet .38s var(--ease) both;
}
.sel-panel.is-closing { animation: sel-out .14s ease both; }
.sel-panel.is-sheet.is-closing { animation: sel-sheet-out .2s ease both; }

.sel-panel:focus, .sel-panel:focus-visible { outline: none; }
.sel-search input:focus-visible { outline: none; }
.sel-grip { display: none; width: 38px; height: 4px; border-radius: 4px; background: rgb(var(--ink) / .18); margin: 9px auto 0; flex: none; }
.is-sheet .sel-grip { display: block; }
.sel-title { display: none; padding: .75rem 1.1rem .35rem; font-size: 14px; font-weight: 600; flex: none; }
.is-sheet .sel-title { display: block; }

.sel-search { padding: .55rem .55rem .25rem; flex: none; }
.is-sheet .sel-search { padding: .35rem 1rem .4rem; }
.sel-search input {
    width: 100%; height: 38px; padding: 0 .8rem; font-size: 14px; border-radius: 10px !important;
    min-height: 0 !important; background: rgb(var(--ink) / .04) !important; border-color: transparent !important;
}
.sel-search input:focus { background: rgb(var(--surface)) !important; }
.is-sheet .sel-search input { height: 44px; font-size: 16px; }

.sel-list { overflow-y: auto; overscroll-behavior: contain; padding: .3rem; flex: 1 1 auto; -webkit-overflow-scrolling: touch; scrollbar-width: thin; }
.is-sheet .sel-list { padding: .3rem .6rem .9rem; }
.sel-group { padding: .6rem .65rem .25rem; font-size: 12px; font-weight: 560; color: rgb(var(--ink) / .45); }
.sel-opt {
    display: flex; align-items: center; gap: .6rem; width: 100%; text-align: left;
    min-height: 38px; padding: .5rem .65rem; border-radius: 9px; cursor: pointer;
    font-size: 14px; line-height: 1.35; color: rgb(var(--ink) / .86);
    transition: background-color .12s var(--ease);
}
.is-sheet .sel-opt { min-height: 50px; padding: .65rem .8rem; font-size: 16px; border-radius: 12px; }
.sel-opt .sel-txt { flex: 1; min-width: 0; overflow-wrap: anywhere; }
.sel-opt .sel-tick { width: 16px; height: 16px; flex: none; stroke-width: 2.2; color: rgb(var(--moss-500)); opacity: 0; transform: scale(.6); transition: opacity .15s, transform .25s var(--ease); }
.sel-opt.is-active { background: rgb(var(--ink) / .06); }
.sel-opt.is-selected { color: rgb(var(--ink)); font-weight: 580; }
.sel-opt.is-selected .sel-tick { opacity: 1; transform: none; }
.dark .sel-opt.is-selected .sel-tick { color: rgb(var(--moss-300)); }
.sel-opt.is-placeholder { color: rgb(var(--ink) / .5); }
.sel-opt[aria-disabled=true] { opacity: .4; cursor: not-allowed; }
.sel-empty { padding: 1.4rem 1rem; text-align: center; font-size: 13.5px; color: rgb(var(--ink) / .45); }

@keyframes sel-fade { from { opacity: 0; } }
@keyframes sel-pop { from { opacity: 0; transform: scale(.97) translateY(-4px); } }
@keyframes sel-out { to { opacity: 0; transform: scale(.98); } }
@keyframes sel-sheet { from { transform: translateY(100%); } }
@keyframes sel-sheet-out { to { transform: translateY(100%); } }

/* Select yang sedang terbuka: tanda panah berputar */
select.sel-open { border-color: rgb(var(--moss-500)) !important; box-shadow: 0 0 0 4px rgb(var(--moss-500) / .14); }
body select { cursor: pointer; text-overflow: ellipsis; max-width: 100%; }
body select:disabled { cursor: not-allowed; opacity: .6; }

/* Select di dalam baris flex tidak boleh mendorong lebar halaman */
body select { min-width: 0; }
@media (max-width: 639px) {
    /* Filter: select penuh lebar di HP, rapi satu kolom */
    .app-content form.flex-wrap > select { flex: 1 1 100%; width: 100%; }
}

@media (prefers-reduced-motion: reduce) {
    .sel-panel, .sel-scrim { animation: none !important; }
}
</style>

<script>
(function () {
    var SHEET_MAX = 639;               // di bawah/sama dengan lebar ini => bottom sheet
    var SEARCH_MIN = 9;                // tampilkan kolom cari bila opsi >= ini
    var TICK = '<svg class="sel-tick" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"/></svg>';
    var cur = null;                    // state dropdown yang sedang terbuka
    var uid = 0;

    var esc = function (s) { return String(s).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); };
    var isSheet = function () { return window.matchMedia('(max-width: ' + SHEET_MAX + 'px)').matches; };
    var eligible = function (s) { return s && s.tagName === 'SELECT' && !s.multiple && !(s.size > 1) && !s.hasAttribute('data-native'); };

    /* Label untuk judul bottom sheet */
    function titleOf(s) {
        var t = s.getAttribute('aria-label');
        if (!t && s.id) { var l = document.querySelector('label[for="' + s.id + '"]'); if (l) t = l.textContent; }
        if (!t) {
            var p = s.parentElement, l2 = p && p.querySelector('label');
            if (l2) t = l2.textContent;
        }
        if (!t) { var f = s.options[0]; t = f && !f.value ? f.textContent : 'Pilih opsi'; }
        return t.replace(/\s+/g, ' ').trim();
    }

    /* Ambil opsi (termasuk optgroup) dari <select> pada saat dibuka, jadi selalu mutakhir */
    function collect(s) {
        var out = [], idx = 0;
        [].forEach.call(s.children, function (n) {
            if (n.tagName === 'OPTGROUP') {
                out.push({ group: n.label });
                [].forEach.call(n.children, function (o) { out.push(opt(o, n.disabled)); });
            } else if (n.tagName === 'OPTION') out.push(opt(n, false));
        });
        function opt(o, gd) { return { i: o.index, label: o.textContent.replace(/\s+/g, ' ').trim(), value: o.value, disabled: o.disabled || gd, hidden: o.hidden }; }
        return out.filter(function (x) { return !x.hidden; });
    }

    function open(s) {
        if (cur) { if (cur.s === s) return close(); close(true); }
        if (s.disabled) return;
        var data = collect(s);
        var real = data.filter(function (d) { return !d.group; });
        var sheet = isSheet();
        var id = 'sel-' + (++uid);

        var scrim = document.createElement('div');
        scrim.className = 'sel-scrim';
        var panel = document.createElement('div');
        panel.className = 'sel-panel' + (sheet ? ' is-sheet' : '');
        panel.setAttribute('role', 'dialog'); panel.setAttribute('aria-label', titleOf(s));
        var searchable = real.length >= SEARCH_MIN;
        panel.innerHTML =
            '<div class="sel-grip"></div><div class="sel-title">' + esc(titleOf(s)) + '</div>' +
            (searchable ? '<div class="sel-search"><input type="search" placeholder="Cari" autocomplete="off" aria-label="Cari opsi" aria-controls="' + id + '"></div>' : '') +
            '<div class="sel-list" role="listbox" id="' + id + '"></div>';
        var list = panel.querySelector('.sel-list');

        cur = { t: Date.now(), s: s, scrim: scrim, panel: panel, list: list, data: data, active: -1, sheet: sheet, q: '' };
        render();
        document.body.appendChild(scrim);
        document.body.appendChild(panel);
        s.classList.add('sel-open');
        s.setAttribute('aria-expanded', 'true');
        place();

        // fokus: kolom cari (desktop saja, supaya keyboard HP tidak menutupi daftar) atau daftar
        var si = panel.querySelector('input');
        if (si && !sheet) si.focus({ preventScroll: true });
        else panel.setAttribute('tabindex', '-1'), panel.focus({ preventScroll: true });

        var sel = list.querySelector('.is-selected');
        if (sel) { setActive(+sel.dataset.pos, true); list.scrollTop = Math.max(0, sel.offsetTop - list.clientHeight / 2 + sel.offsetHeight / 2); }
        else { var first = list.querySelector('.sel-opt:not([aria-disabled=true])'); if (first) setActive(+first.dataset.pos, true); }

        // Event: semua klik di dalam panel tidak boleh menutup modal Alpine (@click.outside)
        var stop = function (e) { e.stopPropagation(); };
        panel.addEventListener('click', stop); scrim.addEventListener('click', function (e) { e.stopPropagation(); if (cur && Date.now() - cur.t > 300) close(); });
        panel.addEventListener('mousedown', function (e) { if (e.target.tagName !== 'INPUT') e.preventDefault(); });
        list.addEventListener('click', function (e) {
            var o = e.target.closest('.sel-opt'); if (o && o.getAttribute('aria-disabled') !== 'true') pick(+o.dataset.idx);
        });
        list.addEventListener('mousemove', function (e) {
            var o = e.target.closest('.sel-opt'); if (o && !o.classList.contains('is-active') && o.getAttribute('aria-disabled') !== 'true') setActive(+o.dataset.pos, false);
        });
        if (si) si.addEventListener('change', stop);
        if (si) si.addEventListener('input', function () { cur.q = si.value.trim().toLowerCase(); render(); var f = list.querySelector('.sel-opt:not([aria-disabled=true])'); if (f) setActive(+f.dataset.pos, true); });
        panel.addEventListener('keydown', onKey);
        window.addEventListener('resize', onResize);
        window.addEventListener('scroll', onScroll, true);
        if (sheet) document.documentElement.style.overflow = 'hidden';
    }

    function render() {
        var c = cur, html = '', pos = 0, n = 0;
        c.data.forEach(function (d) {
            if (d.group) { if (!c.q) html += '<div class="sel-group" role="presentation">' + esc(d.group) + '</div>'; return; }
            if (c.q && d.label.toLowerCase().indexOf(c.q) < 0) return;
            n++;
            var selected = d.i === c.s.selectedIndex;
            html += '<div class="sel-opt' + (selected ? ' is-selected' : '') + (!d.value ? ' is-placeholder' : '') + '" role="option" data-idx="' + d.i + '" data-pos="' + (pos++) + '"' +
                (selected ? ' aria-selected="true"' : '') + (d.disabled ? ' aria-disabled="true"' : '') + '><span class="sel-txt">' + esc(d.label || '-') + '</span>' + TICK + '</div>';
        });
        c.list.innerHTML = html || '<div class="sel-empty">Tidak ada hasil</div>';
        c.active = -1;
    }

    function setActive(pos, scroll) {
        var items = cur.list.querySelectorAll('.sel-opt');
        if (cur.active > -1 && items[cur.active]) items[cur.active].classList.remove('is-active');
        cur.active = pos;
        var el = items[pos]; if (!el) return;
        el.classList.add('is-active');
        if (scroll) el.scrollIntoView({ block: 'nearest' });
    }

    function onKey(e) {
        var items = [].slice.call(cur.list.querySelectorAll('.sel-opt:not([aria-disabled=true])'));
        var at = items.indexOf(cur.list.querySelectorAll('.sel-opt')[cur.active]);
        var go = function (n) { n = Math.max(0, Math.min(items.length - 1, n)); if (items[n]) setActive(+items[n].dataset.pos, true); };
        switch (e.key) {
            case 'ArrowDown': e.preventDefault(); go(at + 1); break;
            case 'ArrowUp': e.preventDefault(); go(at < 0 ? 0 : at - 1); break;
            case 'Home': if (e.target.tagName !== 'INPUT') { e.preventDefault(); go(0); } break;
            case 'End': if (e.target.tagName !== 'INPUT') { e.preventDefault(); go(items.length - 1); } break;
            case 'Enter': e.preventDefault(); if (items[at]) pick(+items[at].dataset.idx); break;
            case 'Tab': close(); break;
            default:
                // ketik huruf untuk melompat (ketika tidak ada kolom cari)
                if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && e.target.tagName !== 'INPUT') {
                    var k = e.key.toLowerCase();
                    for (var j = 1; j <= items.length; j++) { var it = items[(at + j) % items.length]; if (it.textContent.trim().toLowerCase().indexOf(k) === 0) { setActive(+it.dataset.pos, true); break; } }
                }
        }
    }

    /* Posisi desktop: di bawah select, balik ke atas bila ruang kurang */
    function place() {
        var c = cur; if (!c || c.sheet) return;
        var r = c.s.getBoundingClientRect(), vw = innerWidth, vh = innerHeight, gap = 6, pad = 10;
        var minW = Math.max(r.width, 200), maxW = Math.min(vw - pad * 2, 440);
        c.panel.style.minWidth = Math.min(minW, maxW) + 'px';
        c.panel.style.maxWidth = maxW + 'px';
        c.panel.style.width = 'max-content';
        c.panel.style.maxHeight = '';
        var pw = c.panel.offsetWidth;
        var left = Math.min(Math.max(pad, r.left), vw - pw - pad);
        var below = vh - r.bottom - gap - pad, above = r.top - gap - pad;
        var want = Math.min(c.panel.scrollHeight, 340);
        var up = below < Math.min(want, 220) && above > below;
        var room = Math.max(120, up ? above : below);
        c.panel.style.maxHeight = Math.min(340, room) + 'px';
        var ph = c.panel.offsetHeight;
        c.panel.style.left = left + 'px';
        c.panel.style.top = (up ? Math.max(pad, r.top - gap - ph) : r.bottom + gap) + 'px';
        c.panel.style.setProperty('--sel-origin', (up ? 'bottom ' : 'top ') + 'left');
        // di desktop scrim hanya penangkap klik, tidak digelapkan
        c.scrim.style.background = 'transparent'; c.scrim.style.backdropFilter = c.scrim.style.webkitBackdropFilter = 'none';
    }
    function onResize() { if (!cur) return; if (isSheet() !== cur.sheet) { var s = cur.s; close(true); open(s); } else place(); }
    function onScroll(e) { if (cur && !cur.sheet && !cur.panel.contains(e.target)) place(); }

    function pick(i) {
        var s = cur.s;
        close();
        if (s.selectedIndex !== i) {
            s.selectedIndex = i;
            s.dispatchEvent(new Event('input', { bubbles: true }));
            s.dispatchEvent(new Event('change', { bubbles: true }));
        }
        s.focus({ preventScroll: true });
    }

    function close(instant) {
        if (!cur) return;
        var c = cur; cur = null;
        window.removeEventListener('resize', onResize);
        window.removeEventListener('scroll', onScroll, true);
        c.s.classList.remove('sel-open'); c.s.setAttribute('aria-expanded', 'false');
        if (c.sheet) document.documentElement.style.overflow = '';
        var rm = function () { c.panel.remove(); c.scrim.remove(); };
        if (instant === true || matchMedia('(prefers-reduced-motion: reduce)').matches) return rm();
        c.panel.classList.add('is-closing'); c.scrim.style.opacity = 0; c.scrim.style.transition = 'opacity .18s';
        setTimeout(rm, 200);
    }

    /* Escape menutup dropdown saja (bukan modal di belakangnya) */
    window.addEventListener('keydown', function (e) {
        if (cur && e.key === 'Escape') { e.preventDefault(); e.stopImmediatePropagation(); var s = cur.s; close(); s.focus({ preventScroll: true }); }
    }, true);

    /* Menangkap interaksi pada select asli: ganti popup bawaan dengan milik kita.
       mousedown hanya mencegah popup bawaan; panel dibuka pada click supaya
       rangkaian mouseup/click dari satu sentuhan tidak jatuh ke scrim yang baru muncul. */
    document.addEventListener('mousedown', function (e) {
        var s = e.target.closest && e.target.closest('select');
        if (!eligible(s) || e.button) return;
        e.preventDefault();
        if (document.activeElement !== s) s.focus({ preventScroll: true });
    }, true);
    document.addEventListener('click', function (e) {
        var s = e.target.closest && e.target.closest('select');
        if (!eligible(s)) return;
        e.preventDefault();
        open(s);
    }, true);
    document.addEventListener('keydown', function (e) {
        var s = e.target; if (!eligible(s) || cur) return;
        if (e.key === 'Enter' || e.key === ' ' || ((e.key === 'ArrowDown' || e.key === 'ArrowUp') && e.altKey)) { e.preventDefault(); open(s); }
    }, true);

    /* Atribut ARIA untuk select yang ada & yang muncul belakangan (x-for, modal, dll.) */
    function prep(root) {
        (root.matches && root.matches('select') ? [root] : []).concat([].slice.call(root.querySelectorAll ? root.querySelectorAll('select') : [])).forEach(function (s) {
            if (eligible(s) && !s.hasAttribute('aria-haspopup')) { s.setAttribute('aria-haspopup', 'listbox'); s.setAttribute('aria-expanded', 'false'); }
        });
    }
    prep(document);
    new MutationObserver(function (ms) { ms.forEach(function (m) { [].forEach.call(m.addedNodes, function (n) { if (n.nodeType === 1) prep(n); }); }); })
        .observe(document.documentElement, { childList: true, subtree: true });
    addEventListener('pageshow', function () { close(true); });
})();
</script>
