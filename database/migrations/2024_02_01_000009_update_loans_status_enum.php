<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Widen the enum so a loan can sit as "menunggu" (awaiting approval)
        // or "ditolak" (rejected) before it ever becomes "dipinjam".
        DB::statement("ALTER TABLE loans MODIFY status ENUM('menunggu','dipinjam','dikembalikan','terlambat','ditolak') NOT NULL DEFAULT 'menunggu'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE loans MODIFY status ENUM('dipinjam','dikembalikan','terlambat') NOT NULL DEFAULT 'dipinjam'");
    }
};
