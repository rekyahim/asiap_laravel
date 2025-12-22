<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DtSdt extends Model
{
    protected $table      = 'dt_sdt';
    protected $primaryKey = 'ID';

    // dt_sdt memang TIDAK pakai timestamps
    public $timestamps = false;

    protected $fillable = [
        'ID_SDT', 'NOP', 'TAHUN', 'PETUGAS_SDT',
        'ALAMAT_OP', 'BLOK_KAV_NO_OP', 'RT_OP', 'RW_OP', 'KEL_OP', 'KEC_OP',
        'NAMA_WP', 'ALAMAT_WP', 'BLOK_KAV_NO_WP', 'RT_WP', 'RW_WP',
        'KEL_WP', 'KOTA_WP',
        'JATUH_TEMPO', 'TERHUTANG', 'PENGURANGAN',
        'PBB_HARUS_DIBAYAR', 'STATUS',
    ];

    protected $casts = [
        'JATUH_TEMPO' => 'date',
    ];

    /* ==========================================================
       GLOBAL SCOPE STATUS AKTIF
       ========================================================== */
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $q) {
            $q->where('STATUS', 1);
        });
    }

    public function scopeWithInactive(Builder $q): Builder
    {
        return $q->withoutGlobalScope('active');
    }

    /* ==========================================================
       RELASI
       ========================================================== */

    // Relasi ke master SDT
    public function sdt()
    {
        return $this->belongsTo(Sdt::class, 'ID_SDT', 'ID');
    }

    // Semua riwayat status
    public function statusPenyampaian()
    {
        return $this->hasMany(StatusPenyampaian::class, 'ID_DT_SDT', 'ID');
    }

    // Ambil status terakhir (paling penting)
    public function latestStatus()
    {
        return $this->hasOne(StatusPenyampaian::class, 'ID_DT_SDT', 'ID')
            ->latest('id');
    }

    /* ==========================================================
       HELPER: CEK EXPIRED 6 JAM
       ========================================================== */
    public function isExpired(int $hours = 6): bool
    {
        if (!$this->latestStatus || !$this->latestStatus->updated_at) {
            return false; // belum pernah diinput
        }

        return now()->diffInHours($this->latestStatus->updated_at) >= $hours;
    }

    /* ==========================================================
       ACCESSOR RUPIAH
       ========================================================== */
    public function getTerhutangRpAttribute(): string
    {
        return $this->formatRupiah($this->TERHUTANG);
    }

    public function getPenguranganRpAttribute(): string
    {
        return $this->formatRupiah($this->PENGURANGAN);
    }

    public function getPbbHarusDibayarRpAttribute(): string
    {
        return $this->formatRupiah($this->PBB_HARUS_DIBAYAR);
    }

    private function formatRupiah($value): string
    {
        $num = preg_replace('/[^0-9\-]/', '', (string)$value);
        return $num === '' || $num === '-' ? '-' : 'Rp.' . number_format((int)$num, 0, ',', '.');
    }
}
