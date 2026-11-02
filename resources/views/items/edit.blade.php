@extends('layouts.app')
@section('title', 'Ubah barang')
@section('subtitle', $item->name)

@section('content')
<form action="{{ route('items.update', $item) }}" method="POST" class="max-w-3xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    @csrf @method('PUT')

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode barang</label>
            <input type="text" name="code" value="{{ old('code', $item->code) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama barang</label>
            <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kategori</label>
            <select name="category_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $item->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Supplier</label>
            <select name="supplier_id" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Tidak ada</option>
                @foreach ($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id', $item->supplier_id) == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Gudang</label>
            <select name="warehouse_id" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Belum ditentukan</option>
                @foreach ($warehouses as $w)
                    <option value="{{ $w->id }}" @selected(old('warehouse_id', $item->warehouse_id) == $w->id)>{{ $w->name }}{{ $w->is_default ? ' (Utama)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Satuan</label>
            <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Harga beli (Rp)</label>
            <input type="number" step="0.01" min="0" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Stok minimum</label>
            <input type="number" min="0" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Status</label>
            <select name="status" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="aktif" @selected(old('status', $item->status) == 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(old('status', $item->status) == 'nonaktif')>Nonaktif</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Lokasi penyimpanan</label>
            <input type="text" name="location" value="{{ old('location', $item->location) }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('description', $item->description) }}</textarea>
        </div>
    </div>

    <p class="text-xs text-ink-900/40 mt-3">Catatan: stok saat ini ({{ $item->stock }} {{ $item->unit }}) hanya bisa diubah lewat transaksi Barang Masuk / Keluar agar riwayat tetap akurat.</p>

    <div class="flex items-center gap-2 mt-4 pt-5 border-t border-ink-900/8">
        <a href="{{ route('items.show', $item) }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Simpan perubahan</button>
    </div>
</form>
@endsection
