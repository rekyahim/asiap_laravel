<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('status_penyampaian', function (Blueprint $table) {
            // Tambah kolom jika belum ada
            if (!Schema::hasColumn('status_penyampaian', 'ID_SDT')) {
                $table->unsignedBigInteger('ID_SDT')->nullable()->after('id');
            }

            if (!Schema::hasColumn('status_penyampaian', 'ID_DT_SDT')) {
                $table->unsignedBigInteger('ID_DT_SDT')->nullable()->after('ID_SDT');
            }

            // Foreign key (optional, tapi disarankan)
            $table->foreign('ID_SDT')->references('ID')->on('sdt')->onDelete('cascade');
            $table->foreign('ID_DT_SDT')->references('ID')->on('dt_sdt')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('status_penyampaian', function (Blueprint $table) {
            $table->dropForeign(['ID_SDT']);
            $table->dropForeign(['ID_DT_SDT']);
            $table->dropColumn(['ID_SDT', 'ID_DT_SDT']);
        });
    }
};
