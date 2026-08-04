<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::with(['item', 'warehouse'])
            ->where('type', 'masuk')
            ->when($request->q, fn ($q) => $q->where('reference_no', 'like', "%{$request->q}%"))
            ->when($request->from, fn ($q) => $q->whereDate('date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('date', '<=', $request->to))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-in.index', compact('movements'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $nextRef = 'IN-' . now()->format('Ymd') . '-' . str_pad((StockMovement::whereDate('created_at', now())->count()) + 1, 3, '0', STR_PAD_LEFT);

        return view('stock-in.create', compact('items', 'suppliers', 'warehouses', 'nextRef'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_no' => 'required|string|max:30|unique:stock_movements,reference_no',
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'qty' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'party' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data) {
            $item = Item::findOrFail($data['item_id']);
            $warehouseId = $data['warehouse_id'] ?? $item->warehouse_id;

            $data['type'] = 'masuk';
            $data['total'] = ($data['unit_price'] ?? 0) * $data['qty'];
            $data['user_id'] = Auth::id();
            $data['warehouse_id'] = $warehouseId;

            StockMovement::create($data);

            $item->adjustWarehouseStock($warehouseId, $data['qty']);
            if (! $item->warehouse_id && $warehouseId) {
                $item->update(['warehouse_id' => $warehouseId]);
            }
        });

        return redirect()->route('stock-in.index')->with('success', 'Barang masuk berhasil dicatat & stok diperbarui.');
    }

    public function destroy(StockMovement $stockIn)
    {
        DB::transaction(function () use ($stockIn) {
            $stockIn->item->adjustWarehouseStock($stockIn->warehouse_id, -$stockIn->qty);
            $stockIn->delete();
        });

        return back()->with('success', 'Catatan barang masuk dihapus & stok disesuaikan.');
    }
}
