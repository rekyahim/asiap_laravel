<?php

// app/Console/Commands/MapHakMod.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HakAkses;

class MapHakMod extends Command
{
    protected $signature = 'map:hakmod {hak} {mods*} {--off=}';
    protected $description = 'Mapping hak akses ke modul (pivot)';

    public function handle()
    {
        $hak = HakAkses::findOrFail($this->argument('hak'));
        $mods = $this->argument('mods'); // contoh: 1 2 3

        $payload = collect($mods)->mapWithKeys(fn($id)=>[$id=>['status'=>1,'tglpost'=>now()]])->all();
        $hak->moduls()->syncWithoutDetaching($payload);

        if ($off = $this->option('off')) {
            foreach (explode(',', $off) as $id) {
                $hak->moduls()->updateExistingPivot((int)$id, ['status'=>0]);
            }
        }
        $this->info('Berhasil.');
    }
}
