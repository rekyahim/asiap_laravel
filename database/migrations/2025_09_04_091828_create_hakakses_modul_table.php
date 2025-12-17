<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel BELUM ada -> buat
        if (!Schema::hasTable('hakakses_modul')) {
            Schema::create('hakakses_modul', function (Blueprint $table) {
                // Versi kompatibel dengan tabel kamu yang punya kolom id
                $table->bigIncrements('id');
                $table->unsignedBigInteger('hakakses_id')->index();
                $table->unsignedBigInteger('modul_id')->index();
                $table->boolean('status')->default(1);
                $table->timestamp('tglpost')->nullable();

                // Index bantu (opsional, tapi berguna)
                $table->unique(['hakakses_id', 'modul_id'], 'hakakses_modul_unique');
            });
            return;
        }

        // Jika tabel SUDAH ada -> jangan buat lagi, cukup pastikan kolom-kolomnya ada
        Schema::table('hakakses_modul', function (Blueprint $table) {
            if (!Schema::hasColumn('hakakses_modul', 'hakakses_id')) {
                $table->unsignedBigInteger('hakakses_id')->index()->after('id');
            }
            if (!Schema::hasColumn('hakakses_modul', 'modul_id')) {
                $table->unsignedBigInteger('modul_id')->index()->after('hakakses_id');
            }
            if (!Schema::hasColumn('hakakses_modul', 'status')) {
                $table->boolean('status')->default(1)->after('modul_id');
            }
            if (!Schema::hasColumn('hakakses_modul', 'tglpost')) {
                $table->timestamp('tglpost')->nullable()->after('status');
            }
        });

        // Kalau mau tambahkan unique key gabungan bila belum ada (ignore jika sudah)
        try {
            Schema::table('hakakses_modul', function (Blueprint $table) {
                $table->unique(['hakakses_id', 'modul_id'], 'hakakses_modul_unique');
            });
        } catch (\Throwable $e) {
            // abaikan jika sudah ada / tidak kompatibel
        }
    }

    public function down(): void
    {
        // Aman untuk rollback
        Schema::dropIfExists('hakakses_modul');
    }
};
