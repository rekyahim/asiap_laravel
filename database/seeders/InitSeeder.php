<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hak_akses')->insert([
            ['HAKAKSES' => 'Super Admin', 'STATUS' => 1],
            ['HAKAKSES' => 'Koordinator', 'STATUS' => 1],
            ['HAKAKSES' => 'Petugas', 'STATUS' => 1],
            ['HAKAKSES' => 'Admin', 'STATUS' => 1],
        ]);
    }
}
