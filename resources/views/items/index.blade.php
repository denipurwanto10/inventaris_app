@extends('layouts.app')
@section('title', 'Barang')
@section('subtitle', 'Semua barang dan aset di gudang')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, atau barcode"
            class="flex-1 min-w-[180px] rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        <select name="category_id" onchange="this.form.submit()" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <a href="{{ route('items.index', ['filter' => 'low']) }}"
            class="inline-flex items-center text-xs font-semibold px-3 py-2 rounded-lg border {{ request('filter') === 'low' ? 'bg-amber-400/10 border-amber-400 text-amber-500' : 'border-ink-900/15 text-ink-900/60' }}">
            Stok menipis
        </a>
        @if (request()->hasAny(['q', 'category_id', 'filter']))
            <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold px-3 py-2 text-ink-900/40 hover:text-ink-900">Reset</a>
        @endif
    </form>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
    @forelse ($items as $item)
    @php $ratio = $item->min_stock > 0 ? min(100, round($item->stock / ($item->min_stock * 2) * 100)) : 100; @endphp
    <a href="{{ route('items.show', $item) }}" class="bg-white rounded-xl border border-ink-900/8 p-5 group flex flex-col">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="font-semibold truncate group-hover:text-moss-600 transition-colors">{{ $item->name }}</p>
                <p class="text-xs font-mono text-ink-900/45 mt-0.5">{{ $item->code }}</p>
            </div>
            @if ($item->is_low_stock)
                <span class="shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-400/10 text-amber-500">Menipis</span>
            @else
                <span class="shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Aman</span>
            @endif
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-ink-900/55">
            <span>{{ $item->category->name ?? '-' }}</span>
            @if ($item->location)
                <span class="inline-flex items-center gap-1 min-w-0"><svg class="h-3.5 w-3.5 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg><span class="truncate">{{ $item->location }}</span></span>
            @endif
        </div>
        <div class="mt-auto pt-5">
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-semibold stat-figure leading-none">{{ $item->stock }}<span class="text-sm font-normal text-ink-900/45 ml-1">{{ $item->unit }}</span></p>
                <p class="text-xs text-ink-900/45">Min. {{ $item->min_stock }}</p>
            </div>
            <div class="meter {{ $item->is_low_stock ? 'is-low' : '' }} mt-3"><i style="width: {{ max(4, $ratio) }}%"></i></div>
        </div>
    </a>
    @empty
    <p class="text-sm text-ink-900/50 py-16 text-center col-span-full">Tidak ada barang yang cocok.</p>
    @endforelse
</div>

<div class="mt-6">{{ $items->links() }}</div>
@endsection
