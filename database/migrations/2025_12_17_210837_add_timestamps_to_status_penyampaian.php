<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::table('status_penyampaian', function (Blueprint $table) {
        if (!Schema::hasColumn('status_penyampaian', 'created_at')) {
            $table->timestamp('created_at')->nullable();
        }

        if (!Schema::hasColumn('status_penyampaian', 'updated_at')) {
            $table->timestamp('updated_at')->nullable();
        }
    });
}

    /**
     * Reverse the migrations.
     */
   public function down(): void
{
    Schema::table('status_penyampaian', function (Blueprint $table) {
        $table->dropColumn(['created_at', 'updated_at']);
    });
}
};
