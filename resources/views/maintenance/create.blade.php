@extends('layouts.app')
@section('title', 'Ajukan Maintenance')
@section('subtitle', 'Catat perbaikan, servis rutin, atau kalibrasi barang')

@section('content')
<form action="{{ route('maintenance.store') }}" method="POST" class="max-w-2xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    @csrf
    <div class="grid sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode</label>
            <input type="text" name="code" value="{{ old('code', $nextCode) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tipe</label>
            <select name="type" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="perbaikan">Perbaikan</option>
                <option value="servis_rutin">Servis Rutin</option>
                <option value="kalibrasi">Kalibrasi</option>
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Barang</label>
            <select name="item_id" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm">
                <option value="">Pilih barang</option>
                @foreach ($items as $i)
                    <option value="{{ $i->id }}" @selected(old('item_id', request('item_id')) == $i->id)>{{ $i->name }} ({{ $i->code }})</option>
                @endforeach
            </select>
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Deskripsi Masalah / Pekerjaan</label>
            <textarea name="description" rows="3" required class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('description') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Teknisi</label>
            <input type="text" name="technician" value="{{ old('technician') }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Estimasi Biaya (Rp)</label>
            <input type="number" step="0.01" min="0" name="cost" value="{{ old('cost') }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>
        <div>
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Tanggal Selesai (opsional)</label>
            <input type="date" name="end_date" value="{{ old('end_date') }}"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Catatan</label>
            <textarea name="notes" rows="2" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="flex items-center gap-2 mt-6 pt-5 border-t border-ink-900/8">
        <a href="{{ route('maintenance.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</a>
        <button type="submit" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Simpan</button>
    </div>
</form>
@endsection
