<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gudang.in'],
            ['name' => 'Admin Gudang', 'password' => Hash::make('password'), 'role' => 'admin', 'is_active' => true]
        );

        User::firstOrCreate(
            ['email' => 'staff@gudang.in'],
            ['name' => 'Staff Gudang', 'password' => Hash::make('password'), 'role' => 'staff', 'is_active' => true]
        );

        $categories = [
            ['code' => 'ELK', 'name' => 'Elektronik', 'description' => 'Perangkat elektronik & komputer'],
            ['code' => 'ATK', 'name' => 'Alat Tulis Kantor', 'description' => 'Perlengkapan tulis dan administrasi'],
            ['code' => 'FUR', 'name' => 'Furnitur', 'description' => 'Meja, kursi, dan perabot kantor'],
            ['code' => 'RTG', 'name' => 'Rumah Tangga', 'description' => 'Kebutuhan pantry & kebersihan'],
        ];
        foreach ($categories as $c) { Category::firstOrCreate(['code' => $c['code']], $c); }

        $suppliers = [
            ['code' => 'SUP001', 'name' => 'CV Sumber Elektronik Jaya', 'contact_person' => 'Budi Santoso', 'phone' => '021-5550101', 'email' => 'sales@sumberelektronik.co.id', 'address' => 'Jl. Gatot Subroto No. 12, Jakarta'],
            ['code' => 'SUP002', 'name' => 'Toko ATK Sejahtera', 'contact_person' => 'Rina Wulandari', 'phone' => '021-5550202', 'email' => 'order@atksejahtera.com', 'address' => 'Jl. Kebon Jeruk No. 5, Jakarta'],
            ['code' => 'SUP003', 'name' => 'PT Mebel Karya Indonesia', 'contact_person' => 'Agus Prasetyo', 'phone' => '022-6660303', 'email' => 'info@mebelkarya.id', 'address' => 'Jl. Soekarno Hatta No. 88, Bandung'],
        ];
        foreach ($suppliers as $s) { Supplier::firstOrCreate(['code' => $s['code']], $s); }

        $warehouses = [
            ['code' => 'GD-A', 'name' => 'Gudang A', 'address' => 'Lantai 1, Gedung Utama', 'pic' => 'Admin Gudang', 'is_default' => true],
            ['code' => 'GD-B', 'name' => 'Gudang B', 'address' => 'Lantai 1, Sayap Timur', 'pic' => 'Staff Gudang'],
            ['code' => 'GD-C', 'name' => 'Gudang C', 'address' => 'Lantai 1, Sayap Barat', 'pic' => 'Staff Gudang'],
            ['code' => 'PTR-2', 'name' => 'Pantry Lantai 2', 'address' => 'Lantai 2, dekat ruang rapat'],
        ];
        foreach ($warehouses as $w) { Warehouse::firstOrCreate(['code' => $w['code']], $w); }

        $items = [
            ['name' => 'Laptop Lenovo ThinkPad E14', 'category' => 'ELK', 'supplier' => 'SUP001', 'unit' => 'unit', 'price' => 11500000, 'stock' => 8, 'min' => 3, 'location' => 'Gudang A - Rak 1', 'warehouse' => 'GD-A'],
            ['name' => 'Printer Canon Pixma G2010', 'category' => 'ELK', 'supplier' => 'SUP001', 'unit' => 'unit', 'price' => 2350000, 'stock' => 4, 'min' => 2, 'location' => 'Gudang A - Rak 2', 'warehouse' => 'GD-A'],
            ['name' => 'Mouse Wireless Logitech M170', 'category' => 'ELK', 'supplier' => 'SUP001', 'unit' => 'pcs', 'price' => 125000, 'stock' => 3, 'min' => 10, 'location' => 'Gudang A - Rak 3', 'warehouse' => 'GD-A'],
            ['name' => 'Kertas HVS A4 80gsm', 'category' => 'ATK', 'supplier' => 'SUP002', 'unit' => 'rim', 'price' => 52000, 'stock' => 40, 'min' => 15, 'location' => 'Gudang B - Rak 1', 'warehouse' => 'GD-B'],
            ['name' => 'Tinta Printer Epson 003 Hitam', 'category' => 'ATK', 'supplier' => 'SUP002', 'unit' => 'botol', 'price' => 85000, 'stock' => 6, 'min' => 8, 'location' => 'Gudang B - Rak 2', 'warehouse' => 'GD-B'],
            ['name' => 'Kursi Kantor Ergonomis', 'category' => 'FUR', 'supplier' => 'SUP003', 'unit' => 'unit', 'price' => 1450000, 'stock' => 12, 'min' => 4, 'location' => 'Gudang C - Area 1', 'warehouse' => 'GD-C'],
            ['name' => 'Meja Kerja Kayu 120x60', 'category' => 'FUR', 'supplier' => 'SUP003', 'unit' => 'unit', 'price' => 1850000, 'stock' => 7, 'min' => 3, 'location' => 'Gudang C - Area 2', 'warehouse' => 'GD-C'],
            ['name' => 'Dispenser Air Panas & Dingin', 'category' => 'RTG', 'supplier' => null, 'unit' => 'unit', 'price' => 780000, 'stock' => 2, 'min' => 2, 'location' => 'Pantry Lantai 2', 'warehouse' => 'PTR-2'],
        ];

        foreach ($items as $i => $data) {
            $category = Category::where('code', $data['category'])->first();
            $supplier = $data['supplier'] ? Supplier::where('code', $data['supplier'])->first() : null;
            $warehouse = Warehouse::where('code', $data['warehouse'])->first();
            $code = 'BRG-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $item = Item::firstOrCreate(
                ['code' => $code],
                [
                    'barcode' => $code . '-' . strtoupper(Str::random(4)),
                    'name' => $data['name'],
                    'category_id' => $category->id,
                    'supplier_id' => $supplier?->id,
                    'warehouse_id' => $warehouse->id,
                    'unit' => $data['unit'],
                    'purchase_price' => $data['price'],
                    'min_stock' => $data['min'],
                    'stock' => $data['stock'],
                    'location' => $data['location'],
                    'status' => 'aktif',
                ]
            );

            ItemStock::firstOrCreate(
                ['item_id' => $item->id, 'warehouse_id' => $warehouse->id],
                ['qty' => $data['stock']]
            );
        }
    }
}
