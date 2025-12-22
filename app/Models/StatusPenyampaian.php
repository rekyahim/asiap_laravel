<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusPenyampaian extends Model
{
    protected $table = 'status_penyampaian';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'ID_DT_SDT',
        'ID_SDT',
        'ID_PETUGAS',
        'STATUS_PENYAMPAIAN',
        'STATUS_OP',
        'STATUS_WP',
        'NOP_BENAR',
        'KETERANGAN_PETUGAS', // ✅ SATU INI SAJA
        'EVIDENCE',
        'KOORDINAT_OP',
        'NAMA_PENERIMA',
        'HP_PENERIMA',
        'TGL_PENYAMPAIAN',
    ];

    public function dtSdt()
    {
        return $this->belongsTo(DtSdt::class, 'ID_DT_SDT', 'ID');
    }

    public function petugas()
    {
        return $this->belongsTo(Pengguna::class, 'ID_PETUGAS', 'id');
    }
}
