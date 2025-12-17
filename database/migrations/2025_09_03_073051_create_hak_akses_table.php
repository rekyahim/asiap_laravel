<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hak_akses', function (Blueprint $t) {
            $t->bigIncrements('ID');
            $t->string('HAKAKSES', 100)->index();
            $t->boolean('STATUS')->default(true)->index();
            $t->timestamp('TGLPOST')->useCurrent();
            // tanpa created_at/updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hak_akses');
    }
};
