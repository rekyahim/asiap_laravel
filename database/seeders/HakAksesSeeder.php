<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HakAksesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('hak_akses')->insert([
            ['hakakses'=>'Super Administrator','status'=>1,'tglpost'=>'2024-03-07 20:18:13'],
            ['hakakses'=>'Admin OPD',         'status'=>1,'tglpost'=>'2024-03-14 17:01:00'],
            ['hakakses'=>'Admin Bapenda',     'status'=>1,'tglpost'=>'2024-05-03 08:14:10'],
            ['hakakses'=>'Dashbor',           'status'=>1,'tglpost'=>'2025-01-06 08:16:13'],
            ['hakakses'=>'Test',              'status'=>1,'tglpost'=>'2025-08-26 12:16:03'],
        ]);
    }
}
