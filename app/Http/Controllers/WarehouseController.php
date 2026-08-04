<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::withCount('items')
            ->when($request->q, fn ($q) => $q->where('name', 'like', "%{$request->q}%"))
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
        ]);

        if (! Warehouse::exists()) {
            $data['is_default'] = true;
        }

        Warehouse::create($data);

        return back()->with('success', 'Gudang baru berhasil ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code,' . $warehouse->id,
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'pic' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $warehouse->update($data);

        return back()->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function setDefault(Warehouse $warehouse)
    {
        Warehouse::where('is_default', true)->update(['is_default' => false]);
        $warehouse->update(['is_default' => true, 'is_active' => true]);

        return back()->with('success', "{$warehouse->name} dijadikan gudang utama.");
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->items()->exists() || $warehouse->itemStocks()->where('qty', '>', 0)->exists()) {
            return back()->with('error', 'Gudang tidak bisa dihapus karena masih menyimpan barang.');
        }
        if ($warehouse->is_default) {
            return back()->with('error', 'Gudang utama tidak bisa dihapus. Jadikan gudang lain sebagai utama terlebih dahulu.');
        }

        $warehouse->delete();

        return back()->with('success', 'Gudang berhasil dihapus.');
    }
}
