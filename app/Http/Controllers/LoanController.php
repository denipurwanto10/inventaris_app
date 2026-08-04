<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\LoanItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::with('items.item')
            ->when($request->q, fn ($q) => $q->where('borrower_name', 'like', "%{$request->q}%")
                ->orWhere('code', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->approval, fn ($q) => $q->where('approval_status', $request->approval))
            ->latest('loan_date')
            ->paginate(10)
            ->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create()
    {
        $items = Item::where('stock', '>', 0)->orderBy('name')->get();
        $nextCode = 'PJM-' . str_pad((Loan::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('loans.create', compact('items', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:loans,code',
            'borrower_name' => 'required|string|max:255',
            'borrower_division' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:30',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:loan_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        // Stock is only reserved/checked here, not deducted yet — deduction
        // happens once an admin approves the request (see approve()).
        foreach ($data['items'] as $line) {
            $item = Item::findOrFail($line['item_id']);
            if ($item->stock < $line['qty']) {
                return back()->withInput()->with('error', "Stok {$item->name} tidak mencukupi untuk diajukan.");
            }
        }

        DB::transaction(function () use ($data) {
            $loan = Loan::create([
                'code' => $data['code'],
                'borrower_name' => $data['borrower_name'],
                'borrower_division' => $data['borrower_division'] ?? null,
                'phone' => $data['phone'] ?? null,
                'loan_date' => $data['loan_date'],
                'due_date' => $data['due_date'],
                'notes' => $data['notes'] ?? null,
                'status' => 'menunggu',
                'approval_status' => 'menunggu',
                'user_id' => Auth::id(),
            ]);

            foreach ($data['items'] as $line) {
                LoanItem::create([
                    'loan_id' => $loan->id,
                    'item_id' => $line['item_id'],
                    'qty' => $line['qty'],
                ]);
            }
        });

        return redirect()->route('loans.index')->with('success', 'Peminjaman diajukan & menunggu persetujuan admin.');
    }

    public function show(Loan $loan)
    {
        $loan->load('items.item', 'approver');
        return view('loans.show', compact('loan'));
    }

    public function approve(Loan $loan)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        if (! $loan->isPending()) {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        $loan->load('items.item');

        foreach ($loan->items as $li) {
            if ($li->item->stock < $li->qty) {
                return back()->with('error', "Stok {$li->item->name} tidak lagi mencukupi. Tolak atau sesuaikan pengajuan ini.");
            }
        }

        DB::transaction(function () use ($loan) {
            foreach ($loan->items as $li) {
                $li->item->adjustWarehouseStock($li->item->warehouse_id, -$li->qty);
            }

            $loan->update([
                'status' => 'dipinjam',
                'approval_status' => 'disetujui',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        });

        return back()->with('success', 'Peminjaman disetujui & stok telah dikurangi.');
    }

    public function reject(Request $request, Loan $loan)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        if (! $loan->isPending()) {
            return back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        $data = $request->validate([
            'rejection_reason' => 'nullable|string|max:255',
        ]);

        $loan->update([
            'status' => 'ditolak',
            'approval_status' => 'ditolak',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $data['rejection_reason'] ?? null,
        ]);

        return back()->with('success', 'Peminjaman ditolak.');
    }

    public function returnForm(Loan $loan)
    {
        abort_unless($loan->status === 'dipinjam', 404);
        $loan->load('items.item');
        return view('loans.return', compact('loan'));
    }

    public function processReturn(Request $request, Loan $loan)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.returned_qty' => 'required|integer|min:0',
            'items.*.condition_in' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $loan) {
            foreach ($data['items'] as $loanItemId => $line) {
                $loanItem = LoanItem::findOrFail($loanItemId);
                $returnQty = min($line['returned_qty'], $loanItem->qty - $loanItem->returned_qty);

                if ($returnQty > 0) {
                    $loanItem->increment('returned_qty', $returnQty);
                    $loanItem->update(['condition_in' => $line['condition_in'] ?? 'Baik']);
                    $loanItem->item->adjustWarehouseStock($loanItem->item->warehouse_id, $returnQty);
                }
            }

            $allReturned = $loan->items()->whereColumn('returned_qty', '<', 'qty')->doesntExist();

            $loan->update([
                'status' => $allReturned ? 'dikembalikan' : 'dipinjam',
                'return_date' => $allReturned ? now() : null,
            ]);
        });

        return redirect()->route('loans.show', $loan)->with('success', 'Pengembalian barang berhasil diproses.');
    }
}
