@extends('layouts.app')
@section('title', $item->name)
@section('subtitle', 'Kode ' . $item->code)

@section('content')
<div class="grid lg:grid-cols-3 gap-5">
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-extrabold">{{ $item->name }}</h2>
                    <p class="text-xs text-ink-900/40 mt-0.5">{{ $item->category->name ?? '-' }} @if($item->supplier) &middot; Supplier: {{ $item->supplier->name }} @endif</p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('items.edit', $item) }}" class="text-xs font-semibold px-3 py-2 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Ubah</a>
                    <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus barang ini beserta seluruh riwayatnya?')">
                        @csrf @method('DELETE')
                        <button class="text-xs font-semibold px-3 py-2 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50">Hapus</button>
                    </form>
                </div>
            </div>

            @if ($item->description)
                <p class="text-sm text-ink-900/60 mt-4">{{ $item->description }}</p>
            @endif

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-5">
                <div class="rounded-lg bg-ink-900/[0.03] p-3">
                    <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Stok</p>
                    <p class="font-extrabold text-lg {{ $item->is_low_stock ? 'text-rose-500' : '' }}">{{ $item->stock }} <span class="text-xs font-medium text-ink-900/40">{{ $item->unit }}</span></p>
                </div>
                <div class="rounded-lg bg-ink-900/[0.03] p-3">
                    <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Stok Min.</p>
                    <p class="font-extrabold text-lg">{{ $item->min_stock }}</p>
                </div>
                <div class="rounded-lg bg-ink-900/[0.03] p-3">
                    <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Harga Beli</p>
                    <p class="font-extrabold text-lg">Rp{{ number_format($item->purchase_price, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-ink-900/[0.03] p-3">
                    <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Lokasi</p>
                    <p class="font-extrabold text-lg truncate">{{ $item->location ?: '-' }}</p>
                </div>
                <div class="rounded-lg bg-ink-900/[0.03] p-3">
                    <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Gudang</p>
                    <p class="font-extrabold text-lg truncate">{{ $item->warehouse->name ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
            <h3 class="font-bold text-sm mb-3">Riwayat Stok Terbaru</h3>
            <div class="overflow-x-auto -mx-5">
                <table class="w-full text-sm min-w-[480px]">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8">
                            <th class="pb-2 px-5">Tanggal</th><th class="pb-2 px-2">Tipe</th><th class="pb-2 px-2 text-right">Qty</th><th class="pb-2 px-5">Referensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($movements as $m)
                        <tr class="border-b border-ink-900/5 last:border-0">
                            <td class="py-2.5 px-5 text-ink-900/60 whitespace-nowrap">{{ $m->date->format('d M Y') }}</td>
                            <td class="py-2.5 px-2">
                                @if ($m->type === 'masuk')<span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Masuk</span>
                                @else<span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Keluar</span>@endif
                            </td>
                            <td class="py-2.5 px-2 text-right font-mono">{{ $m->qty }}</td>
                            <td class="py-2.5 px-5 font-mono text-xs text-ink-900/40">{{ $m->reference_no }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 text-center text-ink-900/40">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
            <h3 class="font-bold text-sm mb-3">Riwayat Maintenance</h3>
            @forelse ($maintenances as $m)
            <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0 text-sm">
                <div>
                    <p class="font-medium">{{ Str::limit($m->description, 50) }}</p>
                    <p class="text-xs text-ink-900/40">{{ $m->start_date->format('d M Y') }} &middot; {{ ucfirst(str_replace('_',' ',$m->type)) }}</p>
                </div>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $m->status === 'selesai' ? 'bg-moss-50 text-moss-700' : 'bg-amber-400/10 text-amber-500' }}">{{ ucfirst($m->status) }}</span>
            </div>
            @empty
            <p class="text-sm text-ink-900/40 py-4 text-center">Belum pernah maintenance.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-5">
        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
            <p class="text-[11px] uppercase tracking-wide text-ink-900/40 font-semibold mb-3">Stok per Gudang</p>
            @forelse ($stockByWarehouse as $sw)
            <div class="flex items-center justify-between py-2 border-b border-ink-900/5 last:border-0 text-sm">
                <span class="font-medium">{{ $sw->warehouse->name ?? '—' }}</span>
                <span class="font-mono font-semibold">{{ $sw->qty }} {{ $item->unit }}</span>
            </div>
            @empty
            <p class="text-sm text-ink-900/40 py-3 text-center">
                @if ($item->warehouse)
                    Seluruh stok tercatat di <strong>{{ $item->warehouse->name }}</strong> namun belum terperinci. Catat transaksi baru untuk melacak per gudang.
                @else
                    Barang ini belum ditempatkan di gudang manapun.
                @endif
            </p>
            @endforelse
        </div>

        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5 text-center">
            <p class="text-[11px] uppercase tracking-wide text-ink-900/40 font-semibold mb-3">Kode Barcode</p>
            <div id="barcode-target" class="flex justify-center"></div>
            <p class="font-mono text-sm mt-2 tracking-widest">{{ $item->barcode }}</p>
            <a href="{{ route('barcode.label', $item) }}" target="_blank" class="inline-flex mt-3 text-xs font-semibold px-3 py-2 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Cetak Label</a>
        </div>

        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-5">
            <p class="text-[11px] uppercase tracking-wide text-ink-900/40 font-semibold mb-3">Aksi Cepat</p>
            <div class="space-y-2">
                <a href="{{ route('stock-in.create') }}?item_id={{ $item->id }}" class="flex items-center justify-between text-sm font-semibold px-3 py-2.5 rounded-lg border border-ink-900/15 hover:bg-moss-50 hover:border-moss-300">Catat Barang Masuk →</a>
                <a href="{{ route('stock-out.create') }}?item_id={{ $item->id }}" class="flex items-center justify-between text-sm font-semibold px-3 py-2.5 rounded-lg border border-ink-900/15 hover:bg-amber-50 hover:border-amber-300">Catat Barang Keluar →</a>
                <a href="{{ route('maintenance.create') }}?item_id={{ $item->id }}" class="flex items-center justify-between text-sm font-semibold px-3 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Ajukan Maintenance →</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    document.getElementById('barcode-target').appendChild(svg);
    JsBarcode(svg, @json($item->barcode), { width: 2, height: 60, fontSize: 12, margin: 4 });
</script>
@endpush
