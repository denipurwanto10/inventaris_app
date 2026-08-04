<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40); // stok_menipis | jatuh_tempo | terlambat | peminjaman_baru | peminjaman_disetujui | peminjaman_ditolak
            $table->string('title');
            $table->string('message');
            $table->string('url')->nullable();
            $table->string('unique_key')->unique(); // prevents duplicate alerts for the same event/day
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('app_notifications'); }
};
