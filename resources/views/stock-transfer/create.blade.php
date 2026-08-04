@extends('layouts.app')
@section('title', 'Pindahkan Stok Antar Gudang')
@section('subtitle', 'Total stok barang tidak berubah, hanya berpindah lokasi')

@section('content')
<form action="{{ route('stock-transfer.store') }}" method="POST" class="max-w-2xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode Transfer</label>
            <input type="text" name="code" value="{{ old('code', $nextCode) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
            @error('code')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
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
                    <option value="{{ $i->id }}" @selected(old('item_id') == $i->id)>{{ $i->name }} ({{ $i->code }}) — total stok: {{ $i->stock }}</option>
                @endforeach
            </select>
            @error('item_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Dari Gudang</label>
            <select name="from_warehouse_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Pilih gudang asal</option>
                @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(old('from_warehouse_id') == $w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
            @error('from_warehouse_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Ke Gudang</label>
            <select name="to_warehouse_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Pilih gudang tujuan</option>
                @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(old('to_warehouse_id') == $w->id)>{{ $w->name }}</option>
                @endforeach
            </select>
            @error('to_warehouse_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Jumlah</label>
            <input type="number" name="qty" min="1" value="{{ old('qty', 1) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6 pt-5 border-t border-ink-900/8">
        <a href="{{ route('stock-transfer.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Pindahkan Stok</button>
    </div>
</form>
@endsection
