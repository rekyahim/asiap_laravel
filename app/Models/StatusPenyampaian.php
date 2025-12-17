<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StatusPenyampaian extends Model
{
    use LogsActivity;

    protected $table      = 'status_penyampaian';
    protected $primaryKey = 'id';
    public $timestamps    = false;

    protected $fillable = [
        'ID_DT_SDT',
        'ID_SDT',

        // status inti
        'STATUS_PENYAMPAIAN',
        'STATUS_OP',
        'STATUS_WP',
        'NOP_BENAR',

        // keterangan
        'KETERANGAN_PETUGAS',

        // bukti
        'EVIDENCE',
        'KOORDINAT_OP',

        // metadata
        'TGL_PENYAMPAIAN',
        'NAMA_PENERIMA',
        'HP_PENERIMA',
    ];

    protected $casts = [
        'STATUS_PENYAMPAIAN' => 'integer',
        'STATUS_OP'          => 'integer',
        'STATUS_WP'          => 'integer',
        'TGL_PENYAMPAIAN'    => 'datetime',
    ];

    /* =====================================================
     *  SPATIE ACTIVITY LOG (FIX & STABIL)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('penyampaian')
            ->logOnly([
                'STATUS_PENYAMPAIAN',
                'STATUS_OP',
                'STATUS_WP',
                'NOP_BENAR',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) =>
                $this->activityDescription($event)
            );
    }

    /**
     * Deskripsi log yang jelas & konsisten
     */
    protected function activityDescription(string $event): string
    {
        $status = match ((int) $this->STATUS_PENYAMPAIAN) {
            1       => 'Tersampaikan',
            0       => 'Belum Tersampaikan',
            default => 'Status Tidak Diketahui',
        };

        return match ($event) {
            'created' => "Penyampaian SDT dicatat ({$status})",
            'updated' => "Status penyampaian diperbarui ({$status})",
            default => "Penyampaian {$event}",
        };
    }

    /* =====================================================
     *  RELASI
     * ===================================================== */

    public function dtSdt()
    {
        return $this->belongsTo(DtSdt::class, 'ID_DT_SDT', 'ID');
    }

    public function sdt()
    {
        return $this->belongsTo(Sdt::class, 'ID_SDT', 'ID');
    }
}
