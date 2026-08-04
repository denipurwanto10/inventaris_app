<?php

namespace App\Support;

use App\Models\AppNotification;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Support\Carbon;

class Notifier
{
    /**
     * Look at the current state of the data (low stock items, loans that
     * are due soon / overdue / waiting for approval) and create any
     * notifications that don't already exist yet. Safe to call on every
     * request — `unique_key` makes it idempotent, so nothing is duplicated.
     */
    public static function syncAll(): void
    {
        static::syncLowStock();
        static::syncLoanDueDates();
        static::syncPendingApprovals();
    }

    protected static function syncLowStock(): void
    {
        Item::whereColumn('stock', '<=', 'min_stock')->where('status', 'aktif')->each(function (Item $item) {
            AppNotification::firstOrCreate(
                ['unique_key' => "stok:{$item->id}:{$item->stock}"],
                [
                    'type' => 'stok_menipis',
                    'title' => 'Stok menipis',
                    'message' => "{$item->name} tersisa {$item->stock} {$item->unit} (min. {$item->min_stock}).",
                    'url' => route('items.show', $item),
                ]
            );
        });
    }

    protected static function syncLoanDueDates(): void
    {
        $today = Carbon::today();

        Loan::where('status', 'dipinjam')
            ->where('approval_status', 'disetujui')
            ->where('due_date', '<=', $today->copy()->addDays(2))
            ->each(function (Loan $loan) use ($today) {
                $overdue = $loan->due_date->lt($today);

                AppNotification::firstOrCreate(
                    ['unique_key' => ($overdue ? 'pinjam_terlambat:' : 'pinjam_jatuh_tempo:') . $loan->id . ':' . $today->toDateString()],
                    [
                        'type' => $overdue ? 'terlambat' : 'jatuh_tempo',
                        'title' => $overdue ? 'Peminjaman terlambat' : 'Jatuh tempo mendekat',
                        'message' => $overdue
                            ? "{$loan->borrower_name} belum mengembalikan barang, jatuh tempo {$loan->due_date->format('d M Y')}."
                            : "{$loan->borrower_name} harus mengembalikan barang pada {$loan->due_date->format('d M Y')}.",
                        'url' => route('loans.show', $loan),
                    ]
                );
            });
    }

    protected static function syncPendingApprovals(): void
    {
        Loan::where('approval_status', 'menunggu')->each(function (Loan $loan) {
            AppNotification::firstOrCreate(
                ['unique_key' => "pinjam_baru:{$loan->id}"],
                [
                    'type' => 'peminjaman_baru',
                    'title' => 'Peminjaman menunggu persetujuan',
                    'message' => "{$loan->borrower_name} mengajukan peminjaman {$loan->code}.",
                    'url' => route('loans.show', $loan),
                ]
            );
        });
    }
}
