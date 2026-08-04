@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('subtitle', 'Riwayat pengeluaran barang dari gudang')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari no. referensi…"
            class="flex-1 min-w-[160px] rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        <input type="date" name="from" value="{{ request('from') }}" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
        <input type="date" name="to" value="{{ request('to') }}" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
        <button class="text-xs font-semibold px-3 py-2 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Filter</button>
    </form>
    <a href="{{ route('stock-out.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600 transition shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Catat Barang Keluar
    </a>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                <th class="py-3 px-5 font-semibold">Referensi</th>
                <th class="py-3 px-2 font-semibold">Tanggal</th>
                <th class="py-3 px-2 font-semibold">Barang</th>
                <th class="py-3 px-2 font-semibold">Tujuan / Penerima</th>
                <th class="py-3 px-2 font-semibold text-right">Qty</th>
                <th class="py-3 px-5 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $m)
            <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                <td class="py-3 px-5 font-mono text-xs">{{ $m->reference_no }}</td>
                <td class="py-3 px-2 text-ink-900/60 whitespace-nowrap">{{ $m->date->format('d M Y') }}</td>
                <td class="py-3 px-2 font-medium">{{ $m->item->name ?? '—' }}</td>
                <td class="py-3 px-2 text-ink-900/60">{{ $m->party ?: '-' }}</td>
                <td class="py-3 px-2 text-right font-mono">{{ $m->qty }}</td>
                <td class="py-3 px-5 text-right">
                    <form action="{{ route('stock-out.destroy', $m) }}" method="POST" onsubmit="return confirm('Hapus catatan ini? Stok akan dikembalikan.')">
                        @csrf @method('DELETE')
                        <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-10 text-center text-ink-900/40">Belum ada transaksi barang keluar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $movements->links() }}</div>
@endsection
