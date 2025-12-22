<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            UPDATE dt_sdt d
            JOIN pengguna p
              ON LOWER(TRIM(p.NAMA)) = LOWER(TRIM(d.PETUGAS_SDT))
            SET d.PENGGUNA_ID = p.ID
            WHERE d.PENGGUNA_ID IS NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            UPDATE dt_sdt
            SET PENGGUNA_ID = NULL
        ");
    }
};
