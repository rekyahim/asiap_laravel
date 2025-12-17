<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration {
//     private string $fkName  = 'pengguna_hakakses_id_foreign';
//     private string $idxName = 'pengguna_hakakses_id_index';

//     public function up(): void
//     {
//         Schema::table('pengguna', function (Blueprint $t) {
//             // Kolom baru (jika belum ada)
//             if (!Schema::hasColumn('pengguna', 'TGLPOST'))   $t->timestamp('TGLPOST')->nullable()->after('STATUS');
//             if (!Schema::hasColumn('pengguna', 'NIP'))       $t->string('NIP', 50)->nullable()->after('TGLPOST');
//             if (!Schema::hasColumn('pengguna', 'PASSWORD'))  $t->string('PASSWORD', 255)->nullable()->after('NIP');

//             // Relasi HAKAKSES_ID → hak_akses.ID (jika belum ada)
//             if (!Schema::hasColumn('pengguna', 'HAKAKSES_ID')) {
//                 $t->unsignedBigInteger('HAKAKSES_ID')->nullable()->after('NAMA');
//             }
//             // index (nama pasti)
//             $sm = Schema::getConnection()->getDoctrineSchemaManager();
//             $details = $sm->listTableDetails('pengguna');
//             if (!$details->hasIndex($this->idxName)) {
//                 $t->index('HAKAKSES_ID', $this->idxName);
//             }
//             // foreign key (nama pasti)
//             if (!$this->fkExists('pengguna', $this->fkName)) {
//                 $t->foreign('HAKAKSES_ID', $this->fkName)
//                   ->references('ID')->on('hak_akses')
//                   ->nullOnDelete(); // role dihapus → set null
//             }
//         });
//     }

//     public function down(): void
//     {
//         Schema::table('pengguna', function (Blueprint $t) {
//             if ($this->fkExists('pengguna', $this->fkName)) {
//                 $t->dropForeign($this->fkName);
//             }
//             $sm = Schema::getConnection()->getDoctrineSchemaManager();
//             $details = $sm->listTableDetails('pengguna');
//             if ($details->hasIndex($this->idxName)) {
//                 $t->dropIndex($this->idxName);
//             }
//             if (Schema::hasColumn('pengguna', 'HAKAKSES_ID')) $t->dropColumn('HAKAKSES_ID');

//             // Kolom baru (opsional dibalik)
//             foreach (['PASSWORD','NIP','TGLPOST'] as $c) {
//                 if (Schema::hasColumn('pengguna', $c)) $t->dropColumn($c);
//             }
//         });
//     }

//     private function fkExists(string $table, string $fk): bool
//     {
//         try {
//             $fks = Schema::getConnection()->getDoctrineSchemaManager()->listTableForeignKeys($table);
//         } catch (\Throwable $e) { return false; }
//         foreach ($fks as $f) if ($f->getName() === $fk) return true;
//         return false;
//     }
// };

// database/migrations/2025_10_13_000001_add_hakakses_fk_to_pengguna.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengguna', function (Blueprint $t) {
            if (! Schema::hasColumn('pengguna', 'HAKAKSES_ID')) {
                $t->unsignedInteger('HAKAKSES_ID')->nullable()->after('HAKAKSES');
                $t->index('HAKAKSES_ID', 'idx_pengguna_hakakses_id');
            }
        });
    }
    public function down(): void
    {
        Schema::table('pengguna', function (Blueprint $t) {
            if (Schema::hasColumn('pengguna', 'HAKAKSES_ID')) {
                $t->dropForeign(['HAKAKSES_ID']);
                $t->dropIndex('idx_pengguna_hakakses_id');
                $t->dropColumn('HAKAKSES_ID');
            }
        });
    }
};
