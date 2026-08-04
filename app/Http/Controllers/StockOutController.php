<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockOutController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::with(['item', 'warehouse'])
            ->where('type', 'keluar')
            ->when($request->q, fn ($q) => $q->where('reference_no', 'like', "%{$request->q}%"))
            ->when($request->from, fn ($q) => $q->whereDate('date', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('date', '<=', $request->to))
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock-out.index', compact('movements'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $nextRef = 'OUT-' . now()->format('Ymd') . '-' . str_pad((StockMovement::whereDate('created_at', now())->count()) + 1, 3, '0', STR_PAD_LEFT);

        return view('stock-out.create', compact('items', 'warehouses', 'nextRef'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'reference_no' => 'required|string|max:30|unique:stock_movements,reference_no',
            'item_id' => 'required|exists:items,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'qty' => 'required|integer|min:1',
            'party' => 'nullable|string|max:255',
            'pic' => 'nullable|string|max:255',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $item = Item::findOrFail($data['item_id']);
        $warehouseId = $data['warehouse_id'] ?? $item->warehouse_id;

        if ($item->stock < $data['qty']) {
            return back()->withInput()->with('error', "Stok {$item->name} tidak mencukupi. Sisa stok: {$item->stock}.");
        }
        if ($warehouseId && $item->stockInWarehouse($warehouseId) < $data['qty']) {
            return back()->withInput()->with('error', "Stok {$item->name} di gudang terpilih hanya {$item->stockInWarehouse($warehouseId)}.");
        }

        DB::transaction(function () use ($data, $item, $warehouseId) {
            $data['type'] = 'keluar';
            $data['unit_price'] = $item->purchase_price;
            $data['total'] = $item->purchase_price * $data['qty'];
            $data['user_id'] = Auth::id();
            $data['warehouse_id'] = $warehouseId;

            StockMovement::create($data);

            $item->adjustWarehouseStock($warehouseId, -$data['qty']);
        });

        return redirect()->route('stock-out.index')->with('success', 'Barang keluar berhasil dicatat & stok diperbarui.');
    }

    public function destroy(StockMovement $stockOut)
    {
        DB::transaction(function () use ($stockOut) {
            $stockOut->item->adjustWarehouseStock($stockOut->warehouse_id, $stockOut->qty);
            $stockOut->delete();
        });

        return back()->with('success', 'Catatan barang keluar dihapus & stok dikembalikan.');
    }
}
