{{-- Perilaku halus yang dipakai semua halaman. Semua degradasi aman: tanpa JS, konten tetap tampil. --}}
<script>
(function () {
    var d = document.documentElement;
    var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
    var safe = function (fn) { try { return fn(); } catch (e) {} };

    /* Ganti tema dengan perpindahan warna yang halus */
    window.toggleTheme = function () {
        d.classList.add('theme-anim');
        var dark = d.classList.toggle('dark');
        safe(function () { localStorage.setItem('theme', dark ? 'dark' : 'light'); });
        window.dispatchEvent(new CustomEvent('themechange', { detail: { dark: dark } }));
        setTimeout(function () { d.classList.remove('theme-anim'); }, 600);
    };

    /* Header: judul ringkas muncul saat judul besar keluar layar */
    var head = document.querySelector('.app-head');
    var title = document.querySelector('.page-title');
    if (head) {
        if (title && 'IntersectionObserver' in window) {
            new IntersectionObserver(function (e) { head.classList.toggle('is-scrolled', !e[0].isIntersecting); }, { rootMargin: '-64px 0px 0px 0px' }).observe(title);
        } else {
            addEventListener('scroll', function () { head.classList.toggle('is-scrolled', scrollY > 8); }, { passive: true });
        }
    }

    /* Penanda menu aktif meluncur dari posisi halaman sebelumnya */
    var nav = document.querySelector('.nav-scroll');
    var ind = nav && nav.querySelector('.nav-indicator');
    var act = nav && nav.querySelector('.nav-link.is-active');
    if (ind && act) {
        var y = act.offsetTop, prev = safe(function () { return sessionStorage.getItem('navY'); });
        var put = function (v) { ind.style.transform = 'translateY(' + v + 'px)'; };
        put(prev !== null && prev !== undefined ? +prev : y);
        ind.classList.add('is-on');
        if (prev !== null && prev !== undefined && +prev !== y && !reduce) {
            requestAnimationFrame(function () { requestAnimationFrame(function () { ind.classList.add('is-animating'); put(y); }); });
        } else { put(y); }
        safe(function () { sessionStorage.setItem('navY', y); });
        var r = act.getBoundingClientRect(), nr = nav.getBoundingClientRect();
        if (r.bottom > nr.bottom || r.top < nr.top) nav.scrollTop = Math.max(0, y - nav.clientHeight / 2);
    }

    /* Bilah progres tipis saat berpindah halaman */
    var bar = document.createElement('div');
    bar.className = 'nav-progress';
    document.body.appendChild(bar);
    var timer;
    var run = function () {
        clearTimeout(timer);
        bar.className = 'nav-progress'; void bar.offsetWidth; bar.classList.add('is-run');
        timer = setTimeout(done, 7000); /* tautan unduhan tidak berpindah halaman */
    };
    var done = function () { bar.classList.remove('is-run'); bar.classList.add('is-done'); };
    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.button || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (a.target === '_blank' || a.hasAttribute('download') || a.hasAttribute('data-no-progress')) return;
        var u; try { u = new URL(a.href, location.href); } catch (x) { return; }
        if (u.origin !== location.origin || !/^https?:$/.test(u.protocol)) return;
        if (u.pathname === location.pathname && u.search === location.search) return;
        run();
    });
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        run();
        var f = e.target;
        if (f && f.querySelectorAll) f.querySelectorAll('button[type=submit], button:not([type])').forEach(function (b) { setTimeout(function () { b.classList.add('is-busy'); }, 0); });
    });
    addEventListener('pageshow', function (e) { done(); if (e.persisted) document.querySelectorAll('.is-busy').forEach(function (b) { b.classList.remove('is-busy'); }); });

    /* Tabel jadi daftar bertumpuk di layar kecil: isi label dari header kolom */
    document.querySelectorAll('.app-content table').forEach(function (t) {
        var hs = [].map.call(t.querySelectorAll('thead th'), function (h) { return h.textContent.trim(); });
        if (!hs.length) return;
        t.querySelectorAll('tbody tr').forEach(function (tr) {
            [].forEach.call(tr.children, function (td, i) {
                if (td.hasAttribute('colspan')) return;
                var h = hs[i] || '';
                td.dataset.label = h;
                if (/^aksi$/i.test(h) || h === '') td.dataset.role = 'actions';
            });
        });
        t.classList.add('is-stack');
    });

    /* Angka naik perlahan satu kali (hanya elemen yang menandainya) */
    if (!reduce) {
        document.querySelectorAll('[data-count]').forEach(function (el, k) {
            var to = parseFloat(el.dataset.count);
            if (!isFinite(to)) return;
            var dec = +el.dataset.decimals || 0, pre = el.dataset.prefix || '', suf = el.dataset.suffix || '';
            var fmt = function (n) { return pre + n.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec }).replace(/[,.]/g, '<i class="sep">$&</i>') + suf; };
            var t0 = performance.now() + 180 + k * 70, dur = 1200;
            el.innerHTML = fmt(0);
            (function tick(t) {
                var p = Math.min(Math.max((t - t0) / dur, 0), 1);
                el.innerHTML = fmt(to * (1 - Math.pow(1 - p, 4)));
                if (p < 1) requestAnimationFrame(tick);
            })(performance.now());
        });
    }

    /* Pesan sukses menutup sendiri dengan lipatan lembut */
    document.querySelectorAll('.flash[data-autohide]').forEach(function (f) {
        setTimeout(function () {
            f.style.maxHeight = f.offsetHeight + 'px';
            f.style.transition = 'opacity .4s ease, max-height .6s cubic-bezier(.22,1,.36,1), padding .6s cubic-bezier(.22,1,.36,1), margin .6s cubic-bezier(.22,1,.36,1)';
            requestAnimationFrame(function () { f.style.opacity = 0; f.style.maxHeight = 0; f.style.paddingTop = 0; f.style.paddingBottom = 0; f.style.marginBottom = 0; });
        }, 5500);
    });
})();
</script>
