@extends('layouts.app')
@section('title', 'Detail peminjaman')
@section('subtitle', $loan->code)

@section('content')
@php $overdue = $loan->status === 'dipinjam' && $loan->due_date->isPast(); @endphp
<div class="max-w-2xl bg-white rounded-xl border border-ink-900/8 shadow-panel p-6">
    <div class="flex items-start justify-between">
        <div>
            <h2 class="text-lg font-extrabold">{{ $loan->borrower_name }}</h2>
            <p class="text-xs text-ink-900/40">{{ $loan->borrower_division }} @if($loan->phone) &middot; {{ $loan->phone }} @endif</p>
        </div>
        @if ($loan->approval_status === 'menunggu')
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-400/10 text-amber-500">Menunggu persetujuan</span>
        @elseif ($loan->approval_status === 'ditolak')
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-50 text-rose-500">Ditolak</span>
        @elseif ($overdue)
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-rose-50 text-rose-500">Terlambat</span>
        @elseif ($loan->status === 'dipinjam')
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-400/10 text-amber-500">Dipinjam</span>
        @else
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-moss-50 text-moss-700">Dikembalikan</span>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-5">
        <div class="rounded-lg bg-ink-900/[0.03] p-3">
            <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Tgl pinjam</p>
            <p class="font-bold text-sm">{{ $loan->loan_date->format('d M Y') }}</p>
        </div>
        <div class="rounded-lg bg-ink-900/[0.03] p-3">
            <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Jatuh tempo</p>
            <p class="font-bold text-sm">{{ $loan->due_date->format('d M Y') }}</p>
        </div>
        <div class="rounded-lg bg-ink-900/[0.03] p-3">
            <p class="text-[10px] uppercase tracking-wide text-ink-900/40 font-semibold">Dikembalikan</p>
            <p class="font-bold text-sm">{{ $loan->return_date?->format('d M Y') ?? '-' }}</p>
        </div>
    </div>

    <h3 class="font-bold text-sm mt-6 mb-2">Barang dipinjam</h3>
    <div class="overflow-x-auto -mx-6 px-6">
    <table class="w-full text-sm min-w-[480px]">
        <thead>
            <tr class="text-left text-[11px] uppercase tracking-wide text-ink-900/40 border-b border-ink-900/8">
                <th class="py-2">Barang</th><th class="py-2 text-right">Qty</th><th class="py-2 text-right">Sudah kembali</th><th class="py-2">Kondisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loan->items as $li)
            <tr class="border-b border-ink-900/5 last:border-0">
                <td class="py-2.5 font-medium">{{ $li->item->name ?? '-' }}</td>
                <td class="py-2.5 text-right font-mono">{{ $li->qty }}</td>
                <td class="py-2.5 text-right font-mono">{{ $li->returned_qty }}</td>
                <td class="py-2.5 text-ink-900/60">{{ $li->condition_in ?? $li->condition_out }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    @if ($loan->notes)
        <p class="text-sm text-ink-900/60 mt-4"><span class="font-semibold text-ink-900/80">Catatan:</span> {{ $loan->notes }}</p>
    @endif

    @if ($loan->approval_status === 'ditolak' && $loan->rejection_reason)
        <p class="text-sm text-rose-500 mt-2"><span class="font-semibold">Alasan penolakan:</span> {{ $loan->rejection_reason }}</p>
    @endif
    @if ($loan->approval_status !== 'menunggu' && $loan->approver)
        <p class="text-xs text-ink-900/40 mt-2">{{ $loan->approval_status === 'disetujui' ? 'Disetujui' : 'Ditolak' }} oleh {{ $loan->approver->name }} &middot; {{ $loan->approved_at?->format('d M Y H:i') }}</p>
    @endif

    <div class="flex flex-wrap items-center gap-2 mt-6 pt-5 border-t border-ink-900/8" x-data="{ rejecting: false }">
        <a href="{{ route('loans.index') }}" class="text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Kembali</a>

        @if ($loan->isPending() && auth()->user()->isAdmin())
            <form action="{{ route('loans.approve', $loan) }}" method="POST" onsubmit="return confirm('Setujui peminjaman ini? Stok langsung berkurang.')">
                @csrf
                <button class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Setujui</button>
            </form>
            <button type="button" @click="rejecting = true" class="text-sm font-semibold px-5 py-2.5 rounded-lg border border-rose-200 text-rose-500 hover:bg-rose-50">Tolak</button>
        @endif

        @if ($loan->status === 'dipinjam')
            <a href="{{ route('loans.return-form', $loan) }}" class="text-sm font-semibold px-5 py-2.5 rounded-lg bg-moss-600 text-white hover:bg-moss-700">Proses pengembalian</a>
        @endif

        <div x-show="rejecting" x-cloak class="fixed inset-0 z-50 grid place-items-center p-4 bg-black/45 backdrop-blur-[2px]" x-transition:enter="transition-opacity duration-300 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-200 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @keydown.escape.window="rejecting=false">
            <div @click.outside="rejecting=false" class="bg-white rounded-2xl w-full max-w-md shadow-2xl p-6">
                <form action="{{ route('loans.reject', $loan) }}" method="POST">
                    @csrf
                    <h3 class="font-bold text-base mb-4">Tolak peminjaman</h3>
                    <label class="block text-xs font-semibold text-ink-900/70 mb-1.5">Alasan (opsional)</label>
                    <textarea name="rejection_reason" rows="3" class="w-full rounded-lg border border-ink-900/15 px-3.5 py-2 text-sm"></textarea>
                    <div class="flex items-center gap-2 mt-5">
                        <button type="button" @click="rejecting=false" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg border border-ink-900/15 hover:bg-ink-900/5">Batal</button>
                        <button type="submit" class="flex-1 text-sm font-semibold px-4 py-2.5 rounded-lg bg-rose-500 text-white hover:bg-rose-600">Tolak peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
