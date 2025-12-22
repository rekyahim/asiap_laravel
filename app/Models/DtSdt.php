<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class DtSdt extends Model
{
    use LogsActivity;

    protected $table      = 'dt_sdt';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'ID_SDT',
        'PENGGUNA_ID',
        'STATUS',
        'NOP',
        'TAHUN',
        'PETUGAS_SDT',

        // OP
        'ALAMAT_OP',
        'BLOK_KAV_NO_OP',
        'RT_OP',
        'RW_OP',
        'KEL_OP',
        'KEC_OP',

        // WP
        'NAMA_WP',
        'ALAMAT_WP',
        'BLOK_KAV_NO_WP',
        'RT_WP',
        'RW_WP',
        'KEL_WP',
        'KOTA_WP',

        // PBB
        'JATUH_TEMPO',
        'TERHUTANG',
        'PENGURANGAN',
        'PBB_HARUS_DIBAYAR',
    ];

    protected $casts = [
        'JATUH_TEMPO' => 'date',
    ];

    /* =====================================================
     *  SPATIE ACTIVITY LOG (AUTO CRUD ONLY)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('dt_sdt')
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) =>
                $this->activityDescription($event)
            );
    }

    /**
     * Deskripsi log human readable
     */
    protected function activityDescription(string $event): string
    {
        $label = "Detail SDT (NOP {$this->NOP}, Tahun {$this->TAHUN})";

        return match ($event) {
            'created' => "{$label} ditambahkan",
            'updated' => "{$label} diperbarui",
            'deleted' => "{$label} dihapus",
            default => "{$label} {$event}",
        };
    }

    /**
     * 🔥 FORCE EVENT DELETED
     * Jika logical delete (STATUS = 0)
     */
    public function tapActivity(Activity $activity, string $eventName): void
    {
        if (
            $eventName === 'updated'
            && $this->isDirty('STATUS')
            && (int) $this->STATUS === 0
        ) {
            $activity->event       = 'deleted';
            $activity->description = $this->activityDescription('deleted');
        }
    }

    /* =====================================================
     *  GLOBAL SCOPE (DEFAULT: STATUS = 1)
     * ===================================================== */
    protected static function booted()
    {
        static::addGlobalScope('active', function (Builder $query) {
            $query->where('dt_sdt.STATUS', 1);
        });
    }

    /**
     * Ambil data termasuk STATUS = 0
     */
    public function scopeWithInactive(Builder $q): Builder
    {
        return $q->withoutGlobalScope('active');
    }

    /* =====================================================
     *  RELASI
     * ===================================================== */

    /** Induk SDT */
    public function sdt()
    {
        return $this->belongsTo(Sdt::class, 'ID_SDT', 'ID');
    }
    public function pengguna()
    {
        return $this->belongsTo(\App\Models\Pengguna::class, 'PENGGUNA_ID', 'ID');
    }

    /** Riwayat status penyampaian */
    public function statusPenyampaian()
    {
        return $this->hasMany(StatusPenyampaian::class, 'ID_DT_SDT', 'ID');
    }

    /* =====================================================
     *  ACCESSOR FLAG
     * ===================================================== */

    /**
     * Apakah sudah pernah disampaikan
     */
    public function getSudahDisampaikanAttribute(): bool
    {
        return $this->statusPenyampaian()->exists();
    }

    /* =====================================================
     *  ACCESSOR FORMAT RUPIAH
     * ===================================================== */

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

    protected function formatRupiah($value): string
    {
        if ($value === null || $value === '') {
            return '-';
        }

        $num = preg_replace('/[^0-9\-]/', '', (string) $value);

        return ($num === '' || $num === '-')
            ? '-'
            : 'Rp.' . number_format((int) $num, 0, ',', '.');
    }
}
