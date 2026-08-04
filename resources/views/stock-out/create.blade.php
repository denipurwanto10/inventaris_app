@extends('layouts.app')
@section('title', 'Catat Barang Keluar')
@section('subtitle', 'Pengeluaran barang akan mengurangi stok otomatis')

@section('content')
<form action="{{ route('stock-out.store') }}" method="POST" class="max-w-2xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">No. Referensi</label>
            <input type="text" name="reference_no" value="{{ old('reference_no', $nextRef) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
            @error('reference_no')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tanggal</label>
            <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Barang</label>
            <select name="item_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Pilih barang</option>
                @foreach ($items as $i)
                    <option value="{{ $i->id }}" @selected(old('item_id', request('item_id')) == $i->id)>{{ $i->name }} ({{ $i->code }}) — stok tersedia: {{ $i->stock }}</option>
                @endforeach
            </select>
            @error('item_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Jumlah</label>
            <input type="number" name="qty" min="1" value="{{ old('qty', 1) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Gudang Asal</label>
            <select name="warehouse_id" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Ikuti gudang barang</option>
                @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(old('warehouse_id') == $w->id)>{{ $w->name }}{{ $w->is_default ? ' (Utama)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tujuan / Penerima</label>
            <input type="text" name="party" value="{{ old('party') }}" placeholder="Contoh: Divisi Marketing"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Penanggung Jawab</label>
            <input type="text" name="pic" value="{{ old('pic', auth()->user()->name) }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6 pt-5 border-t border-ink-900/8">
        <a href="{{ route('stock-out.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-amber-500 text-white hover:bg-amber-600">Simpan &amp; Kurangi Stok</button>
    </div>
</form>
@endsection
