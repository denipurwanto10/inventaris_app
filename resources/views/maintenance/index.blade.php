@extends('layouts.app')
@section('title', 'Maintenance')
@section('subtitle', 'Perbaikan, servis rutin, dan kalibrasi aset')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode maintenance…"
            class="flex-1 min-w-[180px] rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        <select name="status" onchange="this.form.submit()" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
            <option value="">Semua Status</option>
            <option value="diproses" @selected(request('status')=='diproses')>Diproses</option>
            <option value="selesai" @selected(request('status')=='selesai')>Selesai</option>
        </select>
    </form>
    <a href="{{ route('maintenance.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Ajukan Maintenance
    </a>
</div>

<div x-data="{ completeModal: false, target: null }">
    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                    <th class="py-3 px-5 font-semibold">Kode</th>
                    <th class="py-3 px-2 font-semibold">Barang</th>
                    <th class="py-3 px-2 font-semibold">Tipe</th>
                    <th class="py-3 px-2 font-semibold">Mulai</th>
                    <th class="py-3 px-2 font-semibold">Status</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($maintenances as $m)
                <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                    <td class="py-3 px-5 font-mono text-xs">{{ $m->code }}</td>
                    <td class="py-3 px-2 font-medium">{{ $m->item->name ?? '—' }}</td>
                    <td class="py-3 px-2 text-ink-900/60">{{ ucfirst(str_replace('_', ' ', $m->type)) }}</td>
                    <td class="py-3 px-2 text-ink-900/60 whitespace-nowrap">{{ $m->start_date->format('d M Y') }}</td>
                    <td class="py-3 px-2">
                        @if ($m->status === 'selesai')
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Selesai</span>
                        @elseif ($m->status === 'diproses')
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Diproses</span>
                        @else
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-ink-900/5 text-ink-900/50">Dibatalkan</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right space-x-2">
                        @if ($m->status === 'diproses')
                            <button @click="completeModal = true; target = {{ $m->id }}" class="text-xs font-semibold text-moss-600 hover:text-moss-700">Selesaikan</button>
                        @endif
                        <form action="{{ route('maintenance.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-900/40">Belum ada data maintenance.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $maintenances->links() }}</div>

    <div x-show="completeModal" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/50">
        <div @click.outside="completeModal=false" class="bg-white rounded-2xl w-full max-w-sm shadow-2xl p-6">
            <h3 class="font-bold text-base mb-4">Selesaikan Maintenance</h3>
            <template x-for="m in [{{ $maintenances->pluck('id')->implode(',') ?: 0 }}]" :key="m"></template>
            <form :action="`/maintenance/${target}/selesai`" method="POST">
                @csrf
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="end_date" required value="{{ now()->toDateString() }}"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Biaya (Rp)</label>
                        <input type="number" step="0.01" min="0" name="cost" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Catatan</label>
                        <textarea name="notes" rows="2" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="button" @click="completeModal=false" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</button>
                    <button type="submit" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Tandai Selesai</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
