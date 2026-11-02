@extends('layouts.app')
@section('title', 'Peminjaman baru')
@section('subtitle', 'Diajukan dulu, stok baru berkurang setelah admin menyetujui')

@section('content')
<form action="{{ route('loans.store') }}" method="POST" class="max-w-3xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6"
    x-data="{ rows: [{ item_id: '', qty: 1 }] }">
    @csrf

    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode peminjaman</label>
            <input type="text" name="code" value="{{ old('code', $nextCode) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div></div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama peminjam</label>
            <input type="text" name="borrower_name" value="{{ old('borrower_name') }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Divisi</label>
            <input type="text" name="borrower_division" value="{{ old('borrower_division') }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">No. telepon</label>
            <input type="text" name="phone" value="{{ old('phone') }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div></div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tanggal pinjam</label>
            <input type="date" name="loan_date" value="{{ old('loan_date', now()->toDateString()) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Batas waktu kembali</label>
            <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->toDateString()) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
    </div>

    <div class="mt-6 pt-5 border-t border-ink-900/8">
        <div class="flex items-center justify-between mb-3">
            <label class="block text-xs font-semibold text-ink-900/70">Barang yang dipinjam</label>
            <button type="button" @click="rows.push({ item_id: '', qty: 1 })" class="text-xs font-semibold text-moss-600 hover:text-moss-700">+ Tambah Baris</button>
        </div>

        <template x-for="(row, idx) in rows" :key="idx">
            <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 mb-3 sm:mb-2 pb-3 sm:pb-0 border-b border-ink-900/8 sm:border-0 last:border-0">
                <select :name="`items[${idx}][item_id]`" x-model="row.item_id" required class="w-full sm:w-auto basis-full sm:basis-0 sm:flex-1 min-w-0 rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                    <option value="">Pilih barang</option>
                    @foreach ($items as $i)
                        <option value="{{ $i->id }}">{{ $i->name }} (stok {{ $i->stock }} {{ $i->unit }})</option>
                    @endforeach
                </select>
                <input type="number" :name="`items[${idx}][qty]`" x-model="row.qty" min="1" required class="flex-1 sm:flex-none sm:w-24 min-w-0 rounded-lg border border-ink-900/15 px-3 py-2.5 text-sm">
                <button type="button" @click="rows.length > 1 && rows.splice(idx,1)" class="p-2 shrink-0 text-rose-400 hover:text-rose-500" aria-label="Hapus baris">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                </button>
            </div>
        </template>
    </div>

    <div class="mt-4">
        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Catatan</label>
        <textarea name="notes" rows="2" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('notes') }}</textarea>
    </div>

    <div class="flex flex-col-reverse sm:flex-row sm:items-center gap-2 mt-6 pt-5 border-t border-ink-900/8 [&>*]:text-center">
        <a href="{{ route('loans.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Ajukan peminjaman</button>
    </div>
</form>
@endsection
