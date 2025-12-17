<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sdt', function (Blueprint $t) {
            $t->increments('ID'); // PK
            $t->string('NAMA_SDT', 120)->unique();
            $t->date('TGL_MULAI')->nullable();
            $t->date('TGL_SELESAI')->nullable();
            // $t->unsignedInteger('ID_USER')->nullable(); // penanggung jawab/header (sementara: SYSTEM)

            // $t->foreign('ID_USER')->references('ID')->on('pengguna')->cascadeOnUpdate()->nullOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sdt');
    }
};
