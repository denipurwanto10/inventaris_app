@extends('layouts.app')
@section('title', 'Gudang')
@section('subtitle', 'Kelola lokasi/gudang tempat barang disimpan')

@section('content')
<div x-data="{ modalOpen: false, editing: null, form: { id: null, code: '', name: '', address: '', pic: '', is_active: true } }">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari gudang…"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </form>
        <button @click="modalOpen = true; editing = null; form = { id: null, code: '', name: '', address: '', pic: '', is_active: true }"
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Gudang
        </button>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
        <table class="w-full text-sm min-w-[640px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                    <th class="py-3 px-5 font-semibold">Kode</th>
                    <th class="py-3 px-2 font-semibold">Nama Gudang</th>
                    <th class="py-3 px-2 font-semibold">PIC</th>
                    <th class="py-3 px-2 font-semibold">Jumlah Barang</th>
                    <th class="py-3 px-2 font-semibold">Status</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($warehouses as $w)
                <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                    <td class="py-3 px-5 font-mono text-xs text-ink-900/60">{{ $w->code }}</td>
                    <td class="py-3 px-2">
                        <p class="font-semibold">{{ $w->name }} @if($w->is_default)<span class="ml-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-moss-50 text-moss-700 align-middle">Utama</span>@endif</p>
                        @if ($w->address)
                            <p class="text-xs text-ink-900/40 mt-0.5">{{ Str::limit($w->address, 50) }}</p>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-ink-900/60">{{ $w->pic ?: '-' }}</td>
                    <td class="py-3 px-2">{{ $w->items_count }}</td>
                    <td class="py-3 px-2">
                        @if ($w->is_active)
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Aktif</span>
                        @else
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-ink-900/5 text-ink-900/40">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right space-x-2 whitespace-nowrap">
                        @unless ($w->is_default)
                        <form action="{{ route('warehouses.set-default', $w) }}" method="POST" class="inline" onsubmit="return confirm('Jadikan {{ $w->name }} sebagai gudang utama?')">
                            @csrf
                            <button class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Jadikan Utama</button>
                        </form>
                        @endunless
                        <button @click="modalOpen = true; editing = {{ $w->id }}; form = { id: {{ $w->id }}, code: '{{ $w->code }}', name: `{{ $w->name }}`, address: `{{ $w->address }}`, pic: `{{ $w->pic }}`, is_active: {{ $w->is_active ? 'true' : 'false' }} }"
                            class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Ubah</button>
                        <form action="{{ route('warehouses.destroy', $w) }}" method="POST" class="inline" onsubmit="return confirm('Hapus gudang ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-10 text-center text-ink-900/40">Belum ada gudang. Tambahkan gudang pertama Anda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $warehouses->links() }}</div>

    {{-- Modal --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/50" @keydown.escape.window="modalOpen=false">
        <div @click.outside="modalOpen=false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <form :action="editing ? `/gudang/${editing}` : '{{ route('warehouses.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                <h3 class="font-bold text-base mb-4" x-text="editing ? 'Ubah Gudang' : 'Tambah Gudang'"></h3>
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode</label>
                        <input type="text" name="code" x-model="form.code" required maxlength="20"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="GD-A">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama Gudang</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="Gudang A">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Alamat (opsional)</label>
                        <textarea name="address" x-model="form.address" rows="2"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Penanggung Jawab (opsional)</label>
                        <input type="text" name="pic" x-model="form.pic"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                    </div>
                    <template x-if="editing">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="rounded border-ink-900/20">
                            Gudang aktif
                        </label>
                    </template>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="button" @click="modalOpen=false" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</button>
                    <button type="submit" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
