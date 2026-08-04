@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('subtitle', 'Daftarkan barang baru ke dalam sistem')

@section('content')
<form action="{{ route('items.store') }}" method="POST" class="max-w-3xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    @csrf

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode Barang</label>
            <input type="text" name="code" value="{{ old('code', $nextCode) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
            @error('code')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama Barang</label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Laptop Lenovo ThinkPad E14"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
            @error('name')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kategori</label>
            <select name="category_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Pilih kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Supplier (opsional)</label>
            <select name="supplier_id" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Tidak ada</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Gudang</label>
            <select name="warehouse_id" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Belum ditentukan</option>
                @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(old('warehouse_id') == $w->id)>{{ $w->name }}{{ $w->is_default ? ' (Utama)' : '' }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Satuan</label>
            <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required placeholder="pcs / box / unit"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Harga Beli (Rp)</label>
            <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', 0) }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Stok Awal</label>
            <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Stok Minimum</label>
            <input type="number" min="0" name="min_stock" value="{{ old('min_stock', 5) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Lokasi Penyimpanan</label>
            <input type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Gudang A - Rak 3"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('description') }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6 pt-5 border-t border-ink-900/8">
        <a href="{{ route('items.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Simpan Barang</button>
    </div>
</form>
@endsection
