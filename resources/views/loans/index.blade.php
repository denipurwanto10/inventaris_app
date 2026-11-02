@extends('layouts.app')
@section('title', 'Peminjaman')
@section('subtitle', 'Siapa meminjam apa, dan kapan harus kembali')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama peminjam atau kode"
            class="flex-1 min-w-[180px] rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
            <option value="">Semua status</option>
            <option value="dipinjam" @selected(request('status')=='dipinjam')>Dipinjam</option>
            <option value="dikembalikan" @selected(request('status')=='dikembalikan')>Dikembalikan</option>
        </select>
        <select name="approval" onchange="this.form.submit()" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
            <option value="">Semua persetujuan</option>
            <option value="menunggu" @selected(request('approval')=='menunggu')>Menunggu persetujuan</option>
            <option value="disetujui" @selected(request('approval')=='disetujui')>Disetujui</option>
            <option value="ditolak" @selected(request('approval')=='ditolak')>Ditolak</option>
        </select>
    </form>
    <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Peminjaman baru
    </a>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
    <table class="w-full text-sm min-w-[640px]">
        <thead>
            <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                <th class="py-3 px-5 font-semibold">Kode</th>
                <th class="py-3 px-2 font-semibold">Peminjam</th>
                <th class="py-3 px-2 font-semibold">Barang</th>
                <th class="py-3 px-2 font-semibold">Jatuh tempo</th>
                <th class="py-3 px-2 font-semibold">Status</th>
                <th class="py-3 px-5 font-semibold text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($loans as $loan)
            @php $overdue = $loan->status === 'dipinjam' && $loan->due_date->isPast(); @endphp
            <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                <td class="py-3 px-5 font-mono text-xs">{{ $loan->code }}</td>
                <td class="py-3 px-2">
                    <p class="font-medium">{{ $loan->borrower_name }}</p>
                    <p class="text-xs text-ink-900/40">{{ $loan->borrower_division }}</p>
                </td>
                <td class="py-3 px-2 text-ink-900/60">{{ $loan->items->pluck('item.name')->filter()->take(2)->implode(', ') }}{{ $loan->items->count() > 2 ? ' +'.($loan->items->count()-2) : '' }}</td>
                <td class="py-3 px-2 {{ $overdue ? 'text-rose-500 font-semibold' : 'text-ink-900/60' }}">{{ $loan->due_date->format('d M Y') }}</td>
                <td class="py-3 px-2">
                    @if ($loan->approval_status === 'menunggu')
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Menunggu persetujuan</span>
                    @elseif ($loan->approval_status === 'ditolak')
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-500">Ditolak</span>
                    @elseif ($overdue)
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-500">Terlambat</span>
                    @elseif ($loan->status === 'dipinjam')
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Dipinjam</span>
                    @else
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Dikembalikan</span>
                    @endif
                </td>
                <td class="py-3 px-5 text-right">
                    <a href="{{ route('loans.show', $loan) }}" class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Lihat detail</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="py-10 text-center text-ink-900/40">Belum ada data peminjaman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $loans->links() }}</div>
@endsection
