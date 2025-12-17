<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('dt_sdt', function (Blueprint $t) {
            $t->increments('ID');                 // PK
            $t->unsignedInteger('ID_SDT');        // FK ke sdt.ID

            // --- kolom utama (wajib saat impor) ---
            $t->string('PETUGAS_SDT', 150)->nullable(); 
            $t->string('NOP', 64);
            $t->string('TAHUN', 8);

            $t->string('ALAMAT_OP', 255)->nullable();
            $t->string('BLOK_KAV_NO_OP', 50)->nullable();
            $t->string('RT_OP', 10)->nullable();
            $t->string('RW_OP', 10)->nullable();
            $t->string('KEL_OP', 100)->nullable();
            $t->string('KEC_OP', 100)->nullable();

            $t->string('NAMA_WP', 150)->nullable();
            $t->string('ALAMAT_WP', 255)->nullable();
            $t->string('BLOK_KAV_NO_WP', 50)->nullable();
            $t->string('RT_WP', 10)->nullable();
            $t->string('RW_WP', 10)->nullable();
            $t->string('KEL_WP', 100)->nullable();
            $t->string('KOTA_WP', 100)->nullable();

            $t->date('JATUH_TEMPO')->nullable();
            $t->decimal('TERHUTANG', 16, 2)->nullable();
            $t->decimal('PENGURANGAN', 16, 2)->nullable();
            $t->decimal('PBB_HARUS_DIBAYAR', 16, 2)->nullable();

            // --- index & constraint ---
            $t->foreign('ID_SDT')->references('ID')->on('sdt')->cascadeOnUpdate()->cascadeOnDelete();
            $t->unique(['ID_SDT', 'NOP', 'TAHUN'], 'uq_dt_sdt_per_sdt_nop_tahun');
            $t->index('NOP');
            $t->index('TAHUN');
            $t->index('PETUGAS_SDT');
        });
    }
    public function down(): void {
        Schema::dropIfExists('dt_sdt');
    }
};
