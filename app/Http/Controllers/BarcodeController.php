<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function scanner()
    {
        return view('barcode.scanner');
    }

    public function lookup(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $item = Item::where('barcode', $request->code)
            ->orWhere('code', $request->code)
            ->with(['category', 'supplier'])
            ->first();

        if (! $item) {
            return response()->json(['found' => false], 404);
        }

        return response()->json([
            'found' => true,
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'code' => $item->code,
                'barcode' => $item->barcode,
                'category' => $item->category->name ?? '-',
                'stock' => $item->stock,
                'unit' => $item->unit,
                'location' => $item->location,
                'url' => route('items.show', $item),
            ],
        ]);
    }

    public function label(Item $item)
    {
        return view('barcode.label', compact('item'));
    }
}
