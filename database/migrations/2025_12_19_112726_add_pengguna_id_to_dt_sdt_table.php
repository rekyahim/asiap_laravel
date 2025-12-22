<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dt_sdt', function (Blueprint $table) {

            // 1️⃣ Tambah kolom (nullable dulu supaya aman)
            $table->unsignedBigInteger('PENGGUNA_ID')
                ->nullable()
                ->after('PETUGAS_SDT');

            // 2️⃣ Optional FK (boleh aktifkan nanti)
            // $table->foreign('PENGGUNA_ID')
            //     ->references('ID')
            //     ->on('pengguna')
            //     ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dt_sdt', function (Blueprint $table) {
            // Drop FK dulu kalau dipakai
            // $table->dropForeign(['PENGGUNA_ID']);

            $table->dropColumn('PENGGUNA_ID');
        });
    }
};
