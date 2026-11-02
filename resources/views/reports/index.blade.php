@extends('layouts.app')
@section('title', 'Laporan')
@section('subtitle', 'Angka gudang untuk periode yang dipilih')

@section('content')
<form method="GET" class="flex flex-wrap items-end gap-2 mb-5 bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
    <div>
        <label class="block text-[11px] font-semibold text-ink-900/60 mb-1">Dari tanggal</label>
        <input type="date" name="from" value="{{ $from }}" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-[11px] font-semibold text-ink-900/60 mb-1">Sampai tanggal</label>
        <input type="date" name="to" value="{{ $to }}" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
    </div>
    <button class="text-xs font-semibold px-4 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900">Terapkan</button>
    <div class="ml-auto flex items-center gap-2" x-data="{ exportOpen: false }">
        <div class="relative">
            <button type="button" @click="exportOpen = !exportOpen" class="text-xs font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Unduh CSV</button>
            <div x-show="exportOpen" x-cloak @click.outside="exportOpen=false" class="absolute right-0 mt-2 w-56 bg-white rounded-lg border border-ink-900/8 shadow-2xl overflow-hidden z-30 text-sm">
                <a href="{{ route('reports.export', ['type' => 'stok']) }}" class="block px-4 py-2.5 hover:bg-ink-900/5">Laporan stok barang</a>
                <a href="{{ route('reports.export', ['type' => 'transaksi', 'from' => $from, 'to' => $to]) }}" class="block px-4 py-2.5 hover:bg-ink-900/5">Transaksi periode ini</a>
                <a href="{{ route('reports.export', ['type' => 'peminjaman']) }}" class="block px-4 py-2.5 hover:bg-ink-900/5">Laporan peminjaman</a>
            </div>
        </div>
        <button type="button" onclick="window.print()" class="text-xs font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Cetak atau simpan PDF</button>
    </div>
</form>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Barang masuk</p>
        <p class="text-xl font-extrabold mt-1">{{ number_format($summary['total_masuk']) }} unit</p>
        <p class="text-xs text-ink-900/40">Rp{{ number_format($summary['nilai_masuk'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Barang keluar</p>
        <p class="text-xl font-extrabold mt-1">{{ number_format($summary['total_keluar']) }} unit</p>
        <p class="text-xs text-ink-900/40">Rp{{ number_format($summary['nilai_keluar'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Nilai stok saat ini</p>
        <p class="text-xl font-extrabold mt-1">Rp{{ number_format($summary['nilai_stok'], 0, ',', '.') }}</p>
        <p class="text-xs text-ink-900/40">{{ number_format($summary['total_barang']) }} jenis barang</p>
    </div>
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wide text-ink-900/40">Peminjaman</p>
        <p class="text-xl font-extrabold mt-1">{{ $summary['peminjaman_aktif'] }} aktif</p>
        <p class="text-xs text-rose-500">{{ $summary['peminjaman_terlambat'] }} terlambat @if($summary['peminjaman_menunggu'] > 0) &middot; {{ $summary['peminjaman_menunggu'] }} menunggu @endif</p>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h3 class="font-bold text-sm mb-3">Barang paling banyak keluar</h3>
        @forelse ($topOut as $t)
        <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0 text-sm">
            <span class="font-medium">{{ $t->item->name ?? '-' }}</span>
            <span class="font-mono font-semibold">{{ $t->total_qty }} unit</span>
        </div>
        @empty
        <p class="text-sm text-ink-900/40 py-6 text-center">Tidak ada data pada periode ini.</p>
        @endforelse
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
        <h3 class="font-bold text-sm mb-3">Barang di bawah stok minimum</h3>
        @forelse ($lowStockItems as $item)
        <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0 text-sm">
            <div>
                <p class="font-medium">{{ $item->name }}</p>
                <p class="text-xs text-ink-900/40">{{ $item->category->name ?? '-' }}</p>
            </div>
            <span class="font-mono font-semibold text-rose-500">{{ $item->stock }}/{{ $item->min_stock }}</span>
        </div>
        @empty
        <p class="text-sm text-ink-900/40 py-6 text-center">Semua stok aman.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5 mt-5">
    <h3 class="font-bold text-sm mb-3">Stok per gudang</h3>
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-3">
        @forelse ($stockByWarehouse as $w)
        <div class="rounded-lg bg-ink-900/[0.03] p-3">
            <p class="text-xs font-semibold truncate">{{ $w->name }}</p>
            <p class="text-lg font-extrabold mt-0.5">{{ number_format($w->item_stocks_sum_qty ?? 0) }}</p>
            <p class="text-[11px] text-ink-900/40">unit tersimpan</p>
        </div>
        @empty
        <p class="text-sm text-ink-900/40 py-4 col-span-full text-center">Belum ada gudang terdaftar.</p>
        @endforelse
    </div>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5 mt-5">
    <h3 class="font-bold text-sm mb-3">Detail transaksi periode ini</h3>
    <div class="overflow-x-auto -mx-5">
        <table class="w-full text-sm min-w-[600px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8">
                    <th class="pb-2 px-5">Tanggal</th><th class="pb-2 px-2">Barang</th><th class="pb-2 px-2">Tipe</th><th class="pb-2 px-2 text-right">Qty</th><th class="pb-2 px-5">Referensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $m)
                <tr class="border-b border-ink-900/5 last:border-0">
                    <td class="py-2.5 px-5 text-ink-900/60 whitespace-nowrap">{{ $m->date->format('d M Y') }}</td>
                    <td class="py-2.5 px-2 font-medium">{{ $m->item->name ?? '-' }}</td>
                    <td class="py-2.5 px-2">
                        @if ($m->type === 'masuk')<span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Masuk</span>
                        @else<span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Keluar</span>@endif
                    </td>
                    <td class="py-2.5 px-2 text-right font-mono">{{ $m->qty }}</td>
                    <td class="py-2.5 px-5 font-mono text-xs text-ink-900/40">{{ $m->reference_no }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-ink-900/40">Tidak ada transaksi pada periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
