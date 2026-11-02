@extends('layouts.app')
@section('title', 'Dasbor')
@section('subtitle', 'Ringkasan gudang hari ini')

@section('content')
{{-- Ringkasan: satu panel, bukan enam kartu yang sama --}}
<section class="ledger" aria-label="Ringkasan">
    <div class="ledger-lead">
        <div>
            <p class="lead-label">Unit tersimpan di gudang</p>
            <p class="lead-fig stat-figure" data-count="{{ $totalStock }}">{{ number_format($totalStock) }}</p>
        </div>
        <p class="lead-note">
            @if ($lowStock > 0)
                <span class="pip is-live"></span>
                <span><a href="{{ route('items.index', ['filter' => 'low']) }}">{{ number_format($lowStock) }} barang</a> di bawah stok minimum</span>
            @else
                <span class="pip" style="background:#9fd3ae"></span>
                <span>Semua stok di atas batas minimum</span>
            @endif
        </p>
    </div>

    <div class="ledger-cells">
        <div class="ledger-cell">
            <p class="cell-label">Jenis barang</p>
            <p class="cell-fig stat-figure" data-count="{{ $totalItems }}">{{ number_format($totalItems) }}</p>
            <p class="cell-note">terdaftar</p>
        </div>
        <div class="ledger-cell">
            <p class="cell-label">Nilai stok</p>
            <p class="cell-fig stat-figure" data-count="{{ round($totalStockValue / 1000000, 1) }}" data-decimals="1" data-prefix="Rp" data-suffix=" jt">Rp{{ number_format($totalStockValue / 1000000, 1) }} jt</p>
            <p class="cell-note">harga beli × stok</p>
        </div>
        <div class="ledger-cell">
            <p class="cell-label">
                @if ($lowStock > 0)<span class="pip"></span>@endif
                Stok menipis
            </p>
            <p class="cell-fig stat-figure" data-count="{{ $lowStock }}">{{ number_format($lowStock) }}</p>
            <p class="cell-note">di bawah minimum</p>
        </div>
        <div class="ledger-cell">
            <p class="cell-label">Keluar bulan ini</p>
            <p class="cell-fig stat-figure" data-count="{{ $outThisMonth }}">{{ number_format($outThisMonth) }}</p>
            <p class="cell-note">unit terpakai</p>
        </div>
        <div class="ledger-cell">
            <p class="cell-label">
                @if ($pendingLoans > 0)<span class="pip"></span>@endif
                Peminjaman aktif
            </p>
            <p class="cell-fig stat-figure" data-count="{{ $activeLoans }}">{{ number_format($activeLoans) }}</p>
            <p class="cell-note">
                @if ($pendingLoans > 0)
                    <a href="{{ route('loans.index', ['approval' => 'menunggu']) }}">{{ $pendingLoans }} menunggu persetujuan</a>
                @else
                    tidak ada yang menunggu
                @endif
            </p>
        </div>
        <div class="ledger-cell">
            <p class="cell-label">Maintenance</p>
            <p class="cell-fig stat-figure" data-count="{{ $inProgressMaintenance }}">{{ number_format($inProgressMaintenance) }}</p>
            <p class="cell-note">sedang diproses</p>
        </div>
    </div>
</section>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5 mt-4 lg:mt-5">
    <div class="lg:col-span-2 bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6">
        <div class="flex items-center justify-between gap-3 mb-5">
            <div>
                <h2 class="font-semibold">Arus stok</h2>
                <p class="text-xs text-ink-900/50 mt-0.5">{{ $chartRange }}</p>
            </div>
            <div class="flex items-center gap-4 text-xs text-ink-900/60">
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-moss-500"></span>Masuk</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Keluar</span>
            </div>
        </div>
        <div class="chart-box" style="height: clamp(200px, 30vw, 270px)"><canvas id="flowChart"></canvas></div>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6">
        <h2 class="font-semibold">Perlu restock</h2>
        <p class="text-xs text-ink-900/50 mt-0.5 mb-3">Stok sudah di bawah batas minimum</p>
        @forelse ($lowStockItems as $item)
            @php $ratio = $item->min_stock > 0 ? min(100, round($item->stock / $item->min_stock * 100)) : 0; @endphp
            <a href="{{ route('items.show', $item) }}" class="row-link group">
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium truncate">{{ $item->name }}</p>
                    <div class="meter {{ $item->stock <= 0 ? 'is-out' : 'is-low' }} mt-2"><i style="width: {{ max(4, $ratio) }}%"></i></div>
                </div>
                <span class="stat-figure text-sm font-semibold shrink-0 {{ $item->stock <= 0 ? 'text-rose-500' : 'text-amber-500' }}">{{ $item->stock }}<span class="text-ink-900/35 font-normal">/{{ $item->min_stock }}</span></span>
            </a>
        @empty
            <p class="text-sm text-ink-900/50 py-10 text-center">Semua stok aman.</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-5 mt-4 lg:mt-5">
    <div class="bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6">
        <h2 class="font-semibold mb-4">Nilai stok per kategori</h2>
        <div class="chart-box" style="height: 230px"><canvas id="categoryChart"></canvas></div>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6">
        <h2 class="font-semibold">Paling sering keluar</h2>
        <p class="text-xs text-ink-900/50 mt-0.5 mb-3">90 hari terakhir</p>
        @php $topMax = max(1, (int) ($topMovingItems->max('total_qty') ?? 1)); @endphp
        @forelse ($topMovingItems as $t)
            <div class="py-2.5">
                <div class="flex items-baseline justify-between gap-3">
                    <p class="text-sm font-medium truncate">{{ $t->item->name ?? '-' }}</p>
                    <span class="text-sm font-semibold stat-figure shrink-0">{{ $t->total_qty }}</span>
                </div>
                <div class="meter mt-2"><i style="width: {{ max(4, round($t->total_qty / $topMax * 100)) }}%"></i></div>
            </div>
        @empty
            <p class="text-sm text-ink-900/50 py-10 text-center">Belum ada barang keluar.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6">
        <h2 class="font-semibold mb-4">Isi tiap gudang</h2>
        @forelse ($warehouseStock as $w)
            @php $pct = $totalStock > 0 ? round((($w->item_stocks_sum_qty ?? 0) / $totalStock) * 100) : 0; @endphp
            <div class="py-2.5">
                <div class="flex items-baseline justify-between gap-3 mb-2">
                    <span class="text-sm font-medium truncate">{{ $w->name }}</span>
                    <span class="text-sm text-ink-900/55 stat-figure shrink-0">{{ number_format($w->item_stocks_sum_qty ?? 0) }} <span class="text-ink-900/35">({{ $pct }}%)</span></span>
                </div>
                <div class="meter"><i style="width: {{ max(3, $pct) }}%"></i></div>
            </div>
        @empty
            <p class="text-sm text-ink-900/50 py-10 text-center">Belum ada gudang terdaftar.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 p-5 lg:p-6 mt-4 lg:mt-5">
    <h2 class="font-semibold mb-3">Transaksi terbaru</h2>
    <div class="overflow-x-auto -mx-5 lg:-mx-6">
        <table class="w-full text-sm min-w-[560px]">
            <thead>
                <tr class="text-left border-b border-ink-900/8">
                    <th class="pb-2.5 px-5 lg:px-6 font-medium">Tanggal</th>
                    <th class="pb-2.5 px-2 font-medium">Barang</th>
                    <th class="pb-2.5 px-2 font-medium">Tipe</th>
                    <th class="pb-2.5 px-2 font-medium text-right">Jumlah</th>
                    <th class="pb-2.5 px-5 lg:px-6 font-medium">Referensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentMovements as $m)
                <tr class="border-b border-ink-900/5 last:border-0">
                    <td class="py-3 px-5 lg:px-6 text-ink-900/60 whitespace-nowrap">{{ $m->date->format('d M Y') }}</td>
                    <td class="py-3 px-2 font-medium">{{ $m->item->name ?? '-' }}</td>
                    <td class="py-3 px-2">
                        @if ($m->type === 'masuk')
                            <span class="inline-flex text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Masuk</span>
                        @else
                            <span class="inline-flex text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Keluar</span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-right stat-figure">{{ $m->qty }}</td>
                    <td class="py-3 px-5 lg:px-6 text-ink-900/45 font-mono text-xs">{{ $m->reference_no }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-900/45">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
(function () {
    if (!window.Chart) return;
    var root = document.documentElement;
    var v = function (n) { return 'rgb(' + getComputedStyle(root).getPropertyValue('--' + n).trim().split(/\s+/).join(',') + ')'; };
    var va = function (n, a) { return 'rgb(' + getComputedStyle(root).getPropertyValue('--' + n).trim().split(/\s+/).join(' ') + ' / ' + a + ')'; };
    var reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;

    Chart.defaults.font.family = '"Schibsted Grotesk", system-ui, sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.animation = reduce ? false : { duration: 1100, easing: 'easeOutQuart' };
    Chart.defaults.maintainAspectRatio = false;

    var raw = @json($chartData);
    var months = @json($chartMonths);
    var label = function (m) { var x = /^(\d{4})-(\d{2})/.exec(m); return x ? new Date(+x[1], +x[2] - 1, 1).toLocaleDateString('id-ID', { month: 'short', year: '2-digit' }) : m; };
    var pick = function (type) { return months.map(function (m) { var r = raw.find(function (x) { return x.bulan === m && x.type === type; }); return r ? +r.total : 0; }); };

    var tip = function () {
        return { backgroundColor: v('ink'), titleColor: v('surface'), bodyColor: v('surface'), padding: 10, cornerRadius: 10, boxPadding: 4, displayColors: true, usePointStyle: true, borderWidth: 0 };
    };

    var flow = new Chart(document.getElementById('flowChart'), {
        type: 'bar',
        data: { labels: months.map(label), datasets: [
            { label: 'Masuk', data: pick('masuk'), backgroundColor: v('moss-500'), borderRadius: 6, borderSkipped: false, maxBarThickness: 22 },
            { label: 'Keluar', data: pick('keluar'), backgroundColor: v('amber-400'), borderRadius: 6, borderSkipped: false, maxBarThickness: 22 },
        ] },
        options: {
            interaction: { mode: 'index', intersect: false },
            plugins: { legend: { display: false }, tooltip: tip() },
            scales: {
                y: { beginAtZero: true, border: { display: false }, grid: { color: va('ink', .07) }, ticks: { color: va('ink', .5), maxTicksLimit: 5, padding: 8 } },
                x: { border: { display: false }, grid: { display: false }, ticks: { color: va('ink', .55) } },
            },
        },
    });

    var cat = @json($categoryValue);
    var palette = function () { return [v('moss-500'), v('amber-400'), v('rose-400'), v('moss-300'), v('amber-300'), v('rose-200'), v('moss-700'), v('amber-600')]; };
    var donut = new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: { labels: cat.map(function (c) { return (c.category && c.category.name) || 'Tanpa kategori'; }),
                datasets: [{ data: cat.map(function (c) { return c.total || 0; }), backgroundColor: palette(), borderColor: v('surface'), borderWidth: 3, hoverOffset: 4 }] },
        options: {
            cutout: '68%',
            plugins: { tooltip: tip(), legend: { position: 'right', labels: { boxWidth: 8, boxHeight: 8, usePointStyle: true, color: va('ink', .65), padding: 12, font: { size: 12 } } } },
        },
    });
    if (matchMedia('(max-width: 640px)').matches) donut.options.plugins.legend.position = 'bottom';

    /* ikut berganti saat tema diubah */
    addEventListener('themechange', function () {
        setTimeout(function () {
            flow.data.datasets[0].backgroundColor = v('moss-500');
            flow.data.datasets[1].backgroundColor = v('amber-400');
            flow.options.scales.y.grid.color = va('ink', .07);
            flow.options.scales.y.ticks.color = va('ink', .5);
            flow.options.scales.x.ticks.color = va('ink', .55);
            flow.options.plugins.tooltip = tip();
            donut.data.datasets[0].backgroundColor = palette();
            donut.data.datasets[0].borderColor = v('surface');
            donut.options.plugins.legend.labels.color = va('ink', .65);
            donut.options.plugins.tooltip = tip();
            flow.update('none'); donut.update('none');
        }, 30);
    });
})();
</script>
@endpush
