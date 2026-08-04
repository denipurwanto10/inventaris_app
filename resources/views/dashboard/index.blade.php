@extends('layouts.app')
@section('title', 'Dasbor')
@section('subtitle', 'Ringkasan kondisi gudang & aset hari ini')

@section('content')
<div class="grid grid-cols-2 lg:grid-cols-6 gap-3 lg:gap-4 mb-6">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Total Barang</p>
        <p class="stat-figure text-2xl font-extrabold mt-1">{{ number_format($totalItems) }}</p>
        <p class="text-xs text-ink-900/40 mt-1">{{ number_format($totalStock) }} unit di gudang</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Nilai Stok</p>
        <p class="stat-figure text-2xl font-extrabold mt-1">Rp{{ number_format($totalStockValue / 1000000, 1) }}jt</p>
        <p class="text-xs text-ink-900/40 mt-1">harga beli x stok</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Stok Menipis</p>
        <p class="stat-figure text-2xl font-extrabold mt-1 {{ $lowStock > 0 ? 'text-amber-500' : '' }}">{{ number_format($lowStock) }}</p>
        <p class="text-xs text-ink-900/40 mt-1">di bawah stok minimum</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Keluar Bln Ini</p>
        <p class="stat-figure text-2xl font-extrabold mt-1">{{ number_format($outThisMonth) }}</p>
        <p class="text-xs text-ink-900/40 mt-1">unit terpakai</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Peminjaman</p>
        <p class="stat-figure text-2xl font-extrabold mt-1">{{ number_format($activeLoans) }}</p>
        <p class="text-xs text-ink-900/40 mt-1">
            aktif
            @if ($pendingLoans > 0)
                &middot; <a href="{{ route('loans.index', ['approval' => 'menunggu']) }}" class="text-amber-500 font-semibold hover:underline">{{ $pendingLoans }} menunggu</a>
            @endif
        </p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Maintenance</p>
        <p class="stat-figure text-2xl font-extrabold mt-1">{{ number_format($inProgressMaintenance) }}</p>
        <p class="text-xs text-ink-900/40 mt-1">sedang diproses</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-sm">Arus Stok 6 Bulan Terakhir</h2>
            <div class="flex items-center gap-3 text-[11px] text-ink-900/50">
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-moss-500"></span>Masuk</span>
                <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-amber-400"></span>Keluar</span>
            </div>
        </div>
        <canvas id="flowChart" height="130"></canvas>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h2 class="font-bold text-sm mb-4">Barang Perlu Restock</h2>
        @forelse ($lowStockItems as $item)
            <a href="{{ route('items.show', $item) }}" class="flex items-center justify-between py-2.5 border-b border-ink-900/5 last:border-0 group">
                <div class="min-w-0">
                    <p class="text-sm font-semibold truncate group-hover:text-moss-600">{{ $item->name }}</p>
                    <p class="text-[11px] text-ink-900/40 font-mono">{{ $item->code }}</p>
                </div>
                <span class="text-xs font-bold text-rose-500 shrink-0 ml-2">{{ $item->stock }}/{{ $item->min_stock }}</span>
            </a>
        @empty
            <p class="text-sm text-ink-900/40 py-6 text-center">Semua stok aman 👍</p>
        @endforelse
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mt-4 lg:mt-6">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h2 class="font-bold text-sm mb-4">Nilai Stok per Kategori</h2>
        <canvas id="categoryChart" height="180"></canvas>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h2 class="font-bold text-sm mb-4">Barang Paling Sering Keluar</h2>
        <p class="text-[11px] text-ink-900/40 -mt-3 mb-3">90 hari terakhir</p>
        @forelse ($topMovingItems as $t)
            <div class="flex items-center justify-between py-2.5 border-b border-ink-900/5 last:border-0">
                <p class="text-sm font-semibold truncate">{{ $t->item->name ?? '—' }}</p>
                <span class="text-xs font-bold text-moss-600 shrink-0 ml-2">{{ $t->total_qty }}</span>
            </div>
        @empty
            <p class="text-sm text-ink-900/40 py-6 text-center">Belum ada barang keluar.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h2 class="font-bold text-sm mb-4">Distribusi Stok per Gudang</h2>
        @forelse ($warehouseStock as $w)
            @php $pct = $totalStock > 0 ? round((($w->item_stocks_sum_qty ?? 0) / $totalStock) * 100) : 0; @endphp
            <div class="py-2">
                <div class="flex items-center justify-between text-sm mb-1">
                    <span class="font-medium truncate">{{ $w->name }}</span>
                    <span class="text-xs text-ink-900/50 font-mono">{{ number_format($w->item_stocks_sum_qty ?? 0) }}</span>
                </div>
                <div class="h-1.5 rounded-full bg-ink-900/5 overflow-hidden">
                    <div class="h-full bg-moss-500 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-ink-900/40 py-6 text-center">Belum ada gudang terdaftar.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5 mt-4 lg:mt-6">
    <h2 class="font-bold text-sm mb-4">Transaksi Terbaru</h2>
    <div class="overflow-x-auto -mx-5">
        <table class="w-full text-sm min-w-[560px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8">
                    <th class="pb-2 px-5 font-semibold">Tanggal</th>
                    <th class="pb-2 px-2 font-semibold">Barang</th>
                    <th class="pb-2 px-2 font-semibold">Tipe</th>
                    <th class="pb-2 px-2 font-semibold text-right">Qty</th>
                    <th class="pb-2 px-5 font-semibold">Referensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentMovements as $m)
                <tr class="border-b border-ink-900/5 last:border-0">
                    <td class="py-2.5 px-5 text-ink-900/60 whitespace-nowrap">{{ $m->date->format('d M Y') }}</td>
                    <td class="py-2.5 px-2 font-medium">{{ $m->item->name ?? '—' }}</td>
                    <td class="py-2.5 px-2">
                        @if ($m->type === 'masuk')
                            <span class="inline-flex text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Masuk</span>
                        @else
                            <span class="inline-flex text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Keluar</span>
                        @endif
                    </td>
                    <td class="py-2.5 px-2 text-right font-mono">{{ $m->qty }}</td>
                    <td class="py-2.5 px-5 text-ink-900/40 font-mono text-xs">{{ $m->reference_no }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-ink-900/40">Belum ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const raw = @json($chartData);
    const months = [...new Set(raw.map(r => r.bulan))].sort();
    const masuk = months.map(m => raw.find(r => r.bulan === m && r.type === 'masuk')?.total || 0);
    const keluar = months.map(m => raw.find(r => r.bulan === m && r.type === 'keluar')?.total || 0);

    new Chart(document.getElementById('flowChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [
                { label: 'Masuk', data: masuk, backgroundColor: '#427C49', borderRadius: 2, maxBarThickness: 28 },
                { label: 'Keluar', data: keluar, backgroundColor: '#CB9526', borderRadius: 2, maxBarThickness: 28 },
            ]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#e3e1d5' } },
                x: { grid: { display: false } }
            }
        }
    });

    const categoryRaw = @json($categoryValue);
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: categoryRaw.map(c => c.category?.name ?? 'Tanpa kategori'),
            datasets: [{
                data: categoryRaw.map(c => c.total || 0),
                backgroundColor: ['#427C49', '#CB9526', '#AC4C2A', '#84B98C', '#DDAE55', '#E2B198', '#28492C', '#8C630B'],
                borderWidth: 0,
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 10 } } } },
        }
    });
</script>
@endpush
