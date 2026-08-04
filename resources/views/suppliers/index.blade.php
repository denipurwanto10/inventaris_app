@extends('layouts.app')
@section('title', 'Supplier')
@section('subtitle', 'Daftar mitra pemasok barang')

@section('content')
<div x-data="{ modalOpen: false, editing: null, form: { id: null, code: '', name: '', contact_person: '', phone: '', email: '', address: '' } }">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari supplier…"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </form>
        <button @click="modalOpen = true; editing = null; form = { id: null, code: '', name: '', contact_person: '', phone: '', email: '', address: '' }"
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Supplier
        </button>
    </div>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse ($suppliers as $s)
        <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="font-bold text-sm">{{ $s->name }}</p>
                    <p class="text-[11px] font-mono text-ink-900/40">{{ $s->code }}</p>
                </div>
                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">{{ $s->items_count }} barang</span>
            </div>
            <div class="mt-3 space-y-1 text-xs text-ink-900/60">
                @if ($s->contact_person)<p>👤 {{ $s->contact_person }}</p>@endif
                @if ($s->phone)<p>📞 {{ $s->phone }}</p>@endif
                @if ($s->email)<p>✉️ {{ $s->email }}</p>@endif
            </div>
            <div class="mt-3 pt-3 border-t border-ink-900/5 flex items-center gap-3">
                <button @click="modalOpen = true; editing = {{ $s->id }}; form = { id: {{ $s->id }}, code: '{{ $s->code }}', name: `{{ $s->name }}`, contact_person: `{{ $s->contact_person }}`, phone: '{{ $s->phone }}', email: '{{ $s->email }}', address: `{{ $s->address }}` }"
                    class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Ubah</button>
                <form action="{{ route('suppliers.destroy', $s) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                    @csrf @method('DELETE')
                    <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                </form>
            </div>
        </div>
        @empty
        <p class="text-sm text-ink-900/40 py-10 text-center col-span-full">Belum ada supplier.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $suppliers->links() }}</div>

    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/50" @keydown.escape.window="modalOpen=false">
        <div @click.outside="modalOpen=false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl max-h-[90vh] overflow-y-auto">
            <form :action="editing ? `/suppliers/${editing}` : '{{ route('suppliers.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                <h3 class="font-bold text-base mb-4" x-text="editing ? 'Ubah Supplier' : 'Tambah Supplier'"></h3>
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kode</label>
                        <input type="text" name="code" x-model="form.code" required maxlength="20"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="SUP001">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama Perusahaan</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Kontak Person</label>
                            <input type="text" name="contact_person" x-model="form.contact_person"
                                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Telepon</label>
                            <input type="text" name="phone" x-model="form.phone"
                                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Email</label>
                        <input type="email" name="email" x-model="form.email"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Alamat</label>
                        <textarea name="address" x-model="form.address" rows="2"
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
