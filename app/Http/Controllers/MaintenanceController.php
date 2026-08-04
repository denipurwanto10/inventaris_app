<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $maintenances = Maintenance::with('item')
            ->when($request->q, fn ($q) => $q->where('code', 'like', "%{$request->q}%"))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest('start_date')
            ->paginate(10)
            ->withQueryString();

        return view('maintenance.index', compact('maintenances'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $nextCode = 'MTN-' . str_pad((Maintenance::max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

        return view('maintenance.create', compact('items', 'nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:30|unique:maintenances,code',
            'item_id' => 'required|exists:items,id',
            'type' => 'required|in:perbaikan,servis_rutin,kalibrasi',
            'description' => 'required|string',
            'technician' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = $request->filled('end_date') ? 'selesai' : 'diproses';

        Maintenance::create($data);

        return redirect()->route('maintenance.index')->with('success', 'Data maintenance berhasil ditambahkan.');
    }

    public function complete(Request $request, Maintenance $maintenance)
    {
        $data = $request->validate([
            'end_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data['status'] = 'selesai';
        $maintenance->update($data);

        return back()->with('success', 'Maintenance ditandai selesai.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return back()->with('success', 'Data maintenance dihapus.');
    }
}
