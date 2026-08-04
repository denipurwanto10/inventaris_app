<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'barcode', 'name', 'category_id', 'supplier_id', 'warehouse_id', 'unit',
        'purchase_price', 'min_stock', 'stock', 'location', 'photo',
        'description', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function stocks()
    {
        return $this->hasMany(ItemStock::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Move stock for this item in/out of a specific warehouse, keeping
     * `items.stock` (the total used everywhere else in the app) and the
     * per-warehouse `item_stocks` breakdown in sync.
     *
     * $delta is positive to add stock, negative to remove it.
     */
    public function adjustWarehouseStock(?int $warehouseId, int $delta): void
    {
        if ($warehouseId) {
            DB::transaction(function () use ($warehouseId, $delta) {
                $row = ItemStock::firstOrCreate(
                    ['item_id' => $this->id, 'warehouse_id' => $warehouseId],
                    ['qty' => 0]
                );
                $row->increment('qty', $delta);
                if ($row->qty < 0) {
                    $row->update(['qty' => 0]);
                }
            });
        }

        $this->increment('stock', $delta);
        if ($this->stock < 0) {
            $this->update(['stock' => 0]);
        }
    }

    public function stockInWarehouse(?int $warehouseId): int
    {
        if (! $warehouseId) {
            return 0;
        }

        return (int) $this->stocks()->where('warehouse_id', $warehouseId)->value('qty');
    }

    /**
     * Move qty of this item's stock from one warehouse to another. Unlike
     * adjustWarehouseStock(), this never touches `items.stock` (the total)
     * since the item isn't leaving the building — only its shelf.
     */
    public function moveWarehouseStock(int $fromWarehouseId, int $toWarehouseId, int $qty): void
    {
        DB::transaction(function () use ($fromWarehouseId, $toWarehouseId, $qty) {
            $from = ItemStock::firstOrCreate(['item_id' => $this->id, 'warehouse_id' => $fromWarehouseId], ['qty' => 0]);
            $from->decrement('qty', $qty);
            if ($from->qty < 0) {
                $from->update(['qty' => 0]);
            }

            $to = ItemStock::firstOrCreate(['item_id' => $this->id, 'warehouse_id' => $toWarehouseId], ['qty' => 0]);
            $to->increment('qty', $qty);
        });
    }
}
