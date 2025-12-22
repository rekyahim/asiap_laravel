<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('dt_sdt', function (Blueprint $table) {

            // pastikan kolom ada & nullable
            if (!Schema::hasColumn('dt_sdt', 'PENGGUNA_ID')) {
                $table->unsignedBigInteger('PENGGUNA_ID')->nullable()->after('PETUGAS_SDT');
            }

            // index dulu (penting untuk FK)
            $table->index('PENGGUNA_ID', 'idx_dt_sdt_pengguna');

            // foreign key
            $table->foreign('PENGGUNA_ID', 'fk_dt_sdt_pengguna')
                ->references('ID')
                ->on('pengguna')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('dt_sdt', function (Blueprint $table) {
            $table->dropForeign('fk_dt_sdt_pengguna');
            $table->dropIndex('idx_dt_sdt_pengguna');
        });
    }
};
