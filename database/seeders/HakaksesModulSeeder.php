<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HakaksesModulSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hakakses_modul')->upsert(
            [
                ['hakakses_id'=>8, 'modul_id'=>1, 'status'=>1, 'tglpost'=>now()],
                ['hakakses_id'=>8, 'modul_id'=>2, 'status'=>1, 'tglpost'=>now()],
            ],
            ['hakakses_id', 'modul_id'],     // unique keys
            ['status', 'tglpost']            // fields to update on conflict
        );
        // atau: ->insertOrIgnore([...]); // jika tidak perlu update saat duplikat
    }
}
