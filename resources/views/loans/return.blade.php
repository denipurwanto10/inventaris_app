@extends('layouts.app')
@section('title', 'Proses pengembalian')
@section('subtitle', 'Pinjaman ' . $loan->code . ' atas nama ' . $loan->borrower_name)

@section('content')
<form action="{{ route('loans.return', $loan) }}" method="POST" class="max-w-2xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-4 sm:p-6">
    @csrf
    <p class="text-sm text-ink-900/60 mb-4">Isi jumlah barang yang dikembalikan dan kondisinya. Stok ikut bertambah lagi.</p>

    <div class="space-y-4">
        @foreach ($loan->items as $li)
        @php $sisa = $li->qty - $li->returned_qty; @endphp
        <div class="rounded-lg border border-ink-900/10 p-3.5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-0.5 sm:gap-3 mb-2">
                <p class="font-semibold text-sm">{{ $li->item->name ?? '-' }}</p>
                <span class="text-xs text-ink-900/40">Dipinjam {{ $li->qty }} &middot; Sisa belum kembali: {{ $sisa }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-ink-900/70 mb-1">Jumlah dikembalikan</label>
                    <input type="number" name="items[{{ $li->id }}][returned_qty]" min="0" max="{{ $sisa }}" value="{{ $sisa }}" {{ $sisa === 0 ? 'disabled' : '' }}
                        class="w-full rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-ink-900/70 mb-1">Kondisi barang</label>
                    <select name="items[{{ $li->id }}][condition_in]" class="w-full rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak ringan</option>
                        <option value="Rusak Berat">Rusak berat</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex items-center gap-2 mt-6 pt-5 border-t border-ink-900/8">
        <a href="{{ route('loans.show', $loan) }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Konfirmasi pengembalian</button>
    </div>
</form>
@endsection
