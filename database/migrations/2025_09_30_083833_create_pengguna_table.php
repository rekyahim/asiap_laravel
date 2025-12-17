<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pengguna')) {
            Schema::create('pengguna', function (Blueprint $table) {
                $t->bigIncrements('ID');

                $t->string('USERNAME', 100);
                $t->string('EMAIL', 150)->nullable();

                // 1 = aktif (muncul di web), 0 = nonaktif
                $t->tinyInteger('STATUS')->default(1)->index();

                $t->timestamp('TGLPOST')->nullable();
                $t->string('NIP', 50)->nullable();
                $t->string('PASSWORD', 255)->nullable();
                $t->string('INITIAL_PASSWORD', 255)->nullable();
                $t->string('NAMA', 255);
                $t->string('JABATAN', 50)->nullable();

                // Relasi ke hak akses
                $t->unsignedBigInteger('HAKAKSES_ID')->nullable();
                $t->string('HAKAKSES', 100)->nullable();

                // ⚡ Perubahan utama: KD_PT diganti menjadi KD_UNIT
                $t->tinyInteger('KD_UNIT')->nullable()
                    ->comment('1=BAPENDA, 2=PD I, 3=DALJAK, 4=UPT I, 5=UPT II, 6=UPT III, 7=UPT IV, 8=UPT V, 9=SEKRETARIAT, 10=PD II, 11=P3D');
                $t->string('NAMA_UNIT', 255)->nullable();

                // Kolom opsional
                $t->unsignedBigInteger('ID_FOTO')->nullable();

                // Index dan foreign key
                $t->index('HAKAKSES_ID', 'pengguna_hakakses_id_index');
                $t->foreign('HAKAKSES_ID', 'pengguna_hakakses_id_foreign')
                    ->references('ID')->on('hak_akses')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $t) {
            try { $t->dropForeign('pengguna_hakakses_id_foreign');} catch (\Throwable $e) {}
            try { $t->dropIndex('pengguna_hakakses_id_index');} catch (\Throwable $e) {}
        });
        Schema::dropIfExists('pengguna');
    }
};
