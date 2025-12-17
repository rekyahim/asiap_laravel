<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('modul')->insert([
            ['nama_modul'=>'kelola hakakses',            'lokasi_modul'=>'manage_hakakses',             'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola admin',               'lokasi_modul'=>'manage_admin',                'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'Log',                        'lokasi_modul'=>'logsystem',                   'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola rekening retribusi',  'lokasi_modul'=>'manage_rekening_retribusi',   'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola opd',                 'lokasi_modul'=>'manage_opd',                  'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola sub rekening retribusi','lokasi_modul'=>'manage_subrekening_retribusi','tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola wajib retribusi',     'lokasi_modul'=>'manage_wajib_retribusi',      'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola riwayat retribusi',   'lokasi_modul'=>'manage_riwayat_retribusi',    'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'kelola target retribusi',    'lokasi_modul'=>'manage_target_retribusi',     'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
            ['nama_modul'=>'report dashboard',           'lokasi_modul'=>'report_dashboard',            'tglpost'=>'2024-03-07 20:18:13', 'status'=>1],
        ]);
    }
}
