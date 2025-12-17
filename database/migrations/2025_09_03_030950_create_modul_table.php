<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('modul', function (Blueprint $table) {
            $table->id();
            $table->string('nama_modul', 100);
            $table->string('lokasi_modul', 150)->unique();
            $table->timestamp('tglpost')->useCurrent();  // default NOW()
            $table->boolean('status')->default(true)->index();
            // Tidak pakai created_at/updated_at karena mengikuti contoh di foto
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modul');
    }
};
