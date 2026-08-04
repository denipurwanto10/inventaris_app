<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tracks how much stock of each item sits in each warehouse.
        // items.stock keeps being the source of truth for the TOTAL
        // (used everywhere else in the app); this table only breaks
        // that total down per location.
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->timestamps();
            $table->unique(['item_id', 'warehouse_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('item_stocks'); }
};
