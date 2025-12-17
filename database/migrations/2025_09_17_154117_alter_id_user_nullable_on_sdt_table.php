<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Sesuaikan tipe kolom kalau berbeda (INT di sini)
        DB::statement('ALTER TABLE `sdt` MODIFY `ID_USER` INT NULL DEFAULT NULL');
    }

    public function down(): void
    {
        // Revert ke NOT NULL kalau perlu
        DB::statement('ALTER TABLE `sdt` MODIFY `ID_USER` INT NOT NULL');
    }
};
