@extends('layouts.app')
@section('title', 'Perpindahan gudang')
@section('subtitle', 'Barang yang dipindah dari satu gudang ke gudang lain')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex-1 max-w-xs">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode transfer"
            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
    </form>
    <a href="{{ route('stock-transfer.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Pindahkan stok
    </a>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                <th class="py-3 px-5 font-semibold">Kode</th>
                <th class="py-3 px-2 font-semibold">Tanggal</th>
                <th class="py-3 px-2 font-semibold">Barang</th>
                <th class="py-3 px-2 font-semibold">Dari</th>
                <th class="py-3 px-2 font-semibold">Ke</th>
                <th class="py-3 px-2 font-semibold text-right">Qty</th>
                <th class="py-3 px-5 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transfers as $t)
            <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                <td class="py-3 px-5 font-mono text-xs">{{ $t->code }}</td>
                <td class="py-3 px-2 text-ink-900/60 whitespace-nowrap">{{ $t->date->format('d M Y') }}</td>
                <td class="py-3 px-2 font-medium">{{ $t->item->name ?? '-' }}</td>
                <td class="py-3 px-2 text-ink-900/60">{{ $t->fromWarehouse->name ?? '-' }}</td>
                <td class="py-3 px-2 text-ink-900/60">{{ $t->toWarehouse->name ?? '-' }}</td>
                <td class="py-3 px-2 text-right font-mono">{{ $t->qty }}</td>
                <td class="py-3 px-5 text-right">
                    <form action="{{ route('stock-transfer.destroy', $t) }}" method="POST" onsubmit="return confirm('Batalkan perpindahan ini? Stok kembali ke gudang asal.')">
                        @csrf @method('DELETE')
                        <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Batalkan</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="py-10 text-center text-ink-900/40">Belum ada perpindahan stok antar gudang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $transfers->links() }}</div>
@endsection
