@extends('layouts.app')
@section('title', 'Kategori')
@section('subtitle', 'Kelompokkan barang berdasarkan jenisnya')

@section('content')
<div x-data="{ modalOpen: false, editing: null, form: { id: null, code: '', name: '', description: '' } }">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori…"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </form>
        <button @click="modalOpen = true; editing = null; form = { id: null, code: '', name: '', description: '' }"
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Kategori
        </button>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
        <table class="w-full text-sm min-w-[480px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                    <th class="py-3 px-5 font-semibold">Kode</th>
                    <th class="py-3 px-2 font-semibold">Nama Kategori</th>
                    <th class="py-3 px-2 font-semibold">Jumlah Barang</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $cat)
                <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                    <td class="py-3 px-5 font-mono text-xs text-ink-900/60">{{ $cat->code }}</td>
                    <td class="py-3 px-2">
                        <p class="font-semibold">{{ $cat->name }}</p>
                        @if ($cat->description)
                            <p class="text-xs text-ink-900/40 mt-0.5">{{ Str::limit($cat->description, 60) }}</p>
                        @endif
                    </td>
                    <td class="py-3 px-2">{{ $cat->items_count }}</td>
                    <td class="py-3 px-5 text-right space-x-2">
                        <button @click="modalOpen = true; editing = {{ $cat->id }}; form = { id: {{ $cat->id }}, code: '{{ $cat->code }}', name: `{{ $cat->name }}`, description: `{{ $cat->description }}` }"
                            class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Ubah</button>
                        <form action="{{ route('categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="py-10 text-center text-ink-900/40">Belum ada kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $categories->links() }}</div>

    {{-- Modal --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/50" @keydown.escape.window="modalOpen=false">
        <div @click.outside="modalOpen=false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <form :action="editing ? `/categories/${editing}` : '{{ route('categories.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                <h3 class="font-bold text-base mb-4" x-text="editing ? 'Ubah Kategori' : 'Tambah Kategori'"></h3>
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode</label>
                        <input type="text" name="code" x-model="form.code" required maxlength="20"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="ELK">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama Kategori</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="Elektronik">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Deskripsi (opsional)</label>
                        <textarea name="description" x-model="form.description" rows="2"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40"></textarea>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button type="button" @click="modalOpen=false" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</button>
                    <button type="submit" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
