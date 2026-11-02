@extends('layouts.app')
@section('title', 'Notifikasi')
@section('subtitle', 'Stok menipis, pinjaman jatuh tempo, dan pengajuan yang menunggu')

@section('content')
<div class="flex items-center justify-between gap-3 mb-5">
    <div class="flex gap-2">
        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold px-3 py-2 rounded-lg border {{ !request('filter') ? 'bg-ink-950 text-white border-ink-950' : 'border-ink-900/15 hover:bg-ink-900/5' }}">Semua</a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="text-xs font-semibold px-3 py-2 rounded-lg border {{ request('filter') === 'unread' ? 'bg-ink-950 text-white border-ink-950' : 'border-ink-900/15 hover:bg-ink-900/5' }}">Belum dibaca</a>
    </div>
    <form action="{{ route('notifications.read-all') }}" method="POST">
        @csrf
        <button class="text-xs font-semibold px-3 py-2 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Tandai semua sudah dibaca</button>
    </form>
</div>

<div class="bg-white rounded-xl border border-ink-900/8 shadow-panel divide-y divide-ink-900/5">
    @forelse ($notifications as $n)
    <form action="{{ route('notifications.read', $n) }}" method="POST" class="block">
        @csrf
        <button type="submit" class="w-full text-left px-5 py-4 flex items-start gap-3 hover:bg-ink-900/[0.015] transition {{ $n->read_at ? '' : 'bg-moss-50/40' }}">
            <span class="mt-1 h-2 w-2 rounded-full shrink-0 {{ $n->read_at ? 'bg-ink-900/10' : 'bg-moss-500' }}"></span>
            <span class="flex-1">
                <span class="flex items-center gap-2">
                    <span class="font-semibold text-sm">{{ $n->title }}</span>
                    @php
                        $badge = match($n->type) {
                            'stok_menipis' => ['Stok', 'bg-amber-400/10 text-amber-500'],
                            'jatuh_tempo' => ['Jatuh Tempo', 'bg-amber-400/10 text-amber-500'],
                            'terlambat' => ['Terlambat', 'bg-rose-50 text-rose-500'],
                            'peminjaman_baru' => ['Persetujuan', 'bg-sky-50 text-sky-600'],
                            default => [ucfirst(str_replace('_',' ',$n->type)), 'bg-ink-900/5 text-ink-900/50'],
                        };
                    @endphp
                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full {{ $badge[1] }}">{{ $badge[0] }}</span>
                </span>
                <span class="block text-sm text-ink-900/60 mt-0.5">{{ $n->message }}</span>
                <span class="block text-xs text-ink-900/30 mt-1">{{ $n->created_at->diffForHumans() }}</span>
            </span>
        </button>
    </form>
    @empty
    <p class="py-12 text-center text-ink-900/40 text-sm">Tidak ada notifikasi.</p>
    @endforelse
</div>

<div class="mt-4">{{ $notifications->links() }}</div>
@endsection
