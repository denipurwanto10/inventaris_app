<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    public function index(Request $request)
    {
        $transfers = StockTransfer::with(['item', 'fromWarehouse', 'toWarehouse'])
            ->when($request->q, fn ($q) => $q->where('code', 'like', "%{$request->q}%"))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-transfer.index', compact('transfers'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $nextCode = 'TRF-' . now()->format('Ymd') . '-' . str_pad((StockTransfer::whereDate('created_at', now())->count()) + 1, 3, '0', STR_PAD_LEFT);

        return view('stock-transfer.create', compact('items', 'warehouses', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:stock_transfers,code',
            'item_id' => 'required|exists:items,id',
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|different:from_warehouse_id|exists:warehouses,id',
            'qty' => 'required|integer|min:1',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($data['item_id']);
        $available = $item->stockInWarehouse($data['from_warehouse_id']);

        if ($available < $data['qty']) {
            return back()->withInput()->with('error', "Stok {$item->name} di gudang asal hanya {$available} {$item->unit}.");
        }

        DB::transaction(function () use ($data, $item) {
            StockTransfer::create([
                'code' => $data['code'],
                'item_id' => $item->id,
                'from_warehouse_id' => $data['from_warehouse_id'],
                'to_warehouse_id' => $data['to_warehouse_id'],
                'qty' => $data['qty'],
                'date' => $data['date'],
                'notes' => $data['notes'] ?? null,
                'user_id' => Auth::id(),
            ]);

            // Total stock (items.stock) is unchanged — the item is simply
            // moving from one warehouse to another.
            $item->moveWarehouseStock($data['from_warehouse_id'], $data['to_warehouse_id'], $data['qty']);

            // If the item's home warehouse was fully emptied, and everything
            // moved to the destination, keep the item's "home" in sync too.
            if ($item->warehouse_id == $data['from_warehouse_id'] && $item->stockInWarehouse($data['from_warehouse_id']) === 0) {
                $item->update(['warehouse_id' => $data['to_warehouse_id']]);
            }
        });

        return redirect()->route('stock-transfer.index')->with('success', 'Perpindahan stok antar gudang berhasil dicatat.');
    }

    public function destroy(StockTransfer $stockTransfer)
    {
        DB::transaction(function () use ($stockTransfer) {
            $stockTransfer->item->moveWarehouseStock(
                $stockTransfer->to_warehouse_id,
                $stockTransfer->from_warehouse_id,
                $stockTransfer->qty
            );

            $stockTransfer->delete();
        });

        return back()->with('success', 'Catatan perpindahan stok dihapus & stok dikembalikan ke gudang asal.');
    }
}
