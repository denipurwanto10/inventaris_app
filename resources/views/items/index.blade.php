@extends('layouts.app')
@section('title', 'Barang')
@section('subtitle', 'Seluruh barang & aset yang tercatat')

@section('content')
<div class="flex flex-col lg:flex-row lg:items-center gap-3 justify-between mb-5">
    <form method="GET" class="flex flex-1 flex-wrap gap-2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, kode, atau barcode…"
            class="flex-1 min-w-[180px] rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        <select name="category_id" onchange="this.form.submit()" class="rounded-lg border border-ink-900/15 px-3 py-2 text-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <a href="{{ route('items.index', ['filter' => 'low']) }}"
            class="inline-flex items-center text-xs font-semibold px-3 py-2 rounded-lg border {{ request('filter') === 'low' ? 'bg-amber-400/10 border-amber-400 text-amber-500' : 'border-ink-900/15 text-ink-900/60' }}">
            Stok Menipis
        </a>
        @if (request()->hasAny(['q', 'category_id', 'filter']))
            <a href="{{ route('items.index') }}" class="inline-flex items-center text-xs font-semibold px-3 py-2 text-ink-900/40 hover:text-ink-900">Reset</a>
        @endif
    </form>
    <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Barang Baru
    </a>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
    @forelse ($items as $item)
    <a href="{{ route('items.show', $item) }}" class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4 hover:border-moss-400/50 transition group">
        <div class="flex items-start justify-between gap-2">
            <div class="min-w-0">
                <p class="font-bold text-sm truncate group-hover:text-moss-600">{{ $item->name }}</p>
                <p class="text-[11px] font-mono text-ink-900/40">{{ $item->code }}</p>
            </div>
            @if ($item->is_low_stock)
                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-500">Menipis</span>
            @else
                <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Aman</span>
            @endif
        </div>
        <div class="mt-3 flex items-center gap-2 text-[11px]">
            <span class="px-2 py-0.5 rounded-full bg-ink-900/5 text-ink-900/60 font-medium">{{ $item->category->name ?? '-' }}</span>
            @if ($item->location)<span class="text-ink-900/40">📍 {{ $item->location }}</span>@endif
        </div>
        <div class="mt-3 pt-3 border-t border-ink-900/5 flex items-end justify-between">
            <div>
                <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Stok</p>
                <p class="text-xl font-extrabold stat-figure">{{ $item->stock }}<span class="text-xs font-medium text-ink-900/40"> {{ $item->unit }}</span></p>
            </div>
            <p class="text-xs text-ink-900/40">Min. {{ $item->min_stock }}</p>
        </div>
    </a>
    @empty
    <p class="text-sm text-ink-900/40 py-14 text-center col-span-full">Tidak ada barang yang cocok dengan pencarian.</p>
    @endforelse
</div>

<div class="mt-6">{{ $items->links() }}</div>
@endsection
