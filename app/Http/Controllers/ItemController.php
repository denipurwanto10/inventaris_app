<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with(['category', 'supplier', 'warehouse'])
            ->when($request->q, function ($query) use ($request) {
                $query->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->q}%")
                        ->orWhere('code', 'like', "%{$request->q}%")
                        ->orWhere('barcode', 'like', "%{$request->q}%");
                });
            })
            ->when($request->category_id, fn ($q) => $q->where('category_id', $request->category_id))
            ->when($request->warehouse_id, fn ($q) => $q->where('warehouse_id', $request->warehouse_id))
            ->when($request->filter === 'low', fn ($q) => $q->whereColumn('stock', '<=', 'min_stock'))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('items.index', compact('items', 'categories', 'warehouses'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $nextCode = 'BRG-' . str_pad((Item::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('items.create', compact('categories', 'suppliers', 'warehouses', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:items,code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $data['barcode'] = $data['code'] . '-' . strtoupper(Str::random(4));
        $data['status'] = 'aktif';

        $item = Item::create($data);

        if ($item->warehouse_id && $item->stock > 0) {
            ItemStock::create([
                'item_id' => $item->id,
                'warehouse_id' => $item->warehouse_id,
                'qty' => $item->stock,
            ]);
        }

        return redirect()->route('items.show', $item)->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function show(Item $item)
    {
        $item->load(['category', 'supplier', 'warehouse']);
        $movements = $item->movements()->with('warehouse')->latest('date')->latest('id')->limit(10)->get();
        $maintenances = $item->maintenances()->latest('start_date')->limit(5)->get();
        $stockByWarehouse = $item->stocks()->with('warehouse')->where('qty', '>', 0)->get();

        return view('items.show', compact('item', 'movements', 'maintenances', 'stockByWarehouse'));
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('items.edit', compact('item', 'categories', 'suppliers', 'warehouses'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:items,code,' . $item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'unit' => 'required|string|max:20',
            'purchase_price' => 'nullable|numeric|min:0',
            'min_stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $item->update($data);

        return redirect()->route('items.show', $item)->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus.');
    }
}
