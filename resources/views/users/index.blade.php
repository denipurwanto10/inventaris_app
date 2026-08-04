@extends('layouts.app')
@section('title', 'Pengguna')
@section('subtitle', 'Kelola akun & hak akses pengguna aplikasi')

@section('content')
<div x-data="{ modalOpen: false, editing: null, form: { id: null, name: '', email: '', role: 'staff' } }">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3 justify-between mb-5">
        <form method="GET" class="flex-1 max-w-xs">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/email…"
                class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
        </form>
        <button @click="modalOpen = true; editing = null; form = { id: null, name: '', email: '', role: 'staff' }"
            class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2.5 rounded-lg bg-ink-950 text-white hover:bg-ink-900 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Pengguna
        </button>
    </div>

    <div class="bg-white rounded-xl border border-ink-900/8 shadow-panel overflow-x-auto">
        <table class="w-full text-sm min-w-[560px]">
            <thead>
                <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8 bg-ink-900/[0.02]">
                    <th class="py-3 px-5 font-semibold">Nama</th>
                    <th class="py-3 px-2 font-semibold">Email</th>
                    <th class="py-3 px-2 font-semibold">Peran</th>
                    <th class="py-3 px-2 font-semibold">Status</th>
                    <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $u)
                <tr class="border-b border-ink-900/5 last:border-0 hover:bg-ink-900/[0.015]">
                    <td class="py-3 px-5 font-semibold">{{ $u->name }} @if($u->id === auth()->id())<span class="text-[10px] text-ink-900/40 font-normal">(Anda)</span>@endif</td>
                    <td class="py-3 px-2 text-ink-900/60">{{ $u->email }}</td>
                    <td class="py-3 px-2">
                        <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full {{ $u->role === 'admin' ? 'bg-ink-950 text-white' : 'bg-ink-900/5 text-ink-900/60' }}">{{ ucfirst($u->role) }}</span>
                    </td>
                    <td class="py-3 px-2">
                        @if ($u->is_active)
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-moss-50 text-moss-700">Aktif</span>
                        @else
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-rose-50 text-rose-500">Nonaktif</span>
                        @endif
                    </td>
                    <td class="py-3 px-5 text-right space-x-2 whitespace-nowrap">
                        <button @click="modalOpen = true; editing = {{ $u->id }}; form = { id: {{ $u->id }}, name: `{{ $u->name }}`, email: `{{ $u->email }}`, role: '{{ $u->role }}' }"
                            class="text-xs font-semibold text-ink-900/60 hover:text-moss-600">Ubah</button>
                        @unless ($u->id === auth()->id())
                        <form action="{{ route('users.toggle-active', $u) }}" method="POST" class="inline">
                            @csrf
                            <button class="text-xs font-semibold text-ink-900/60 hover:text-amber-500">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                        <form action="{{ route('users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengguna ini?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-semibold text-rose-500 hover:text-rose-600">Hapus</button>
                        </form>
                        @endunless
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-10 text-center text-ink-900/40">Belum ada pengguna lain.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    {{-- Modal --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-ink-950/50" @keydown.escape.window="modalOpen=false">
        <div @click.outside="modalOpen=false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
            <form :action="editing ? `/pengguna/${editing}` : '{{ route('users.store') }}'" method="POST" class="p-6">
                @csrf
                <template x-if="editing"><input type="hidden" name="_method" value="PUT"></template>
                <h3 class="font-bold text-base mb-4" x-text="editing ? 'Ubah Pengguna' : 'Tambah Pengguna'"></h3>
                <div class="space-y-3.5">
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Email</label>
                        <input type="email" name="email" x-model="form.email" required
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Peran</label>
                        <select name="role" x-model="form.role" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm">
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                        <p class="text-[11px] text-ink-900/40 mt-1">Admin dapat mengelola gudang, pengguna, dan menyetujui/menolak peminjaman.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">
                            <span x-show="!editing">Kata Sandi</span>
                            <span x-show="editing">Kata Sandi Baru (opsional)</span>
                        </label>
                        <input type="password" name="password" :required="!editing"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40" placeholder="Min. 8 karakter">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Konfirmasi Kata Sandi</label>
                        <input type="password" name="password_confirmation" :required="!editing"
                            class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-moss-500/40">
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
