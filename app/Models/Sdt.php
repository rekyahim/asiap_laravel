<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Sdt extends Model
{
    use LogsActivity;

    protected $table      = 'sdt';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $keyType   = 'int';
    public $incrementing = true;

    protected $fillable = [
        'NAMA_SDT',
        'TGL_MULAI',
        'TGL_SELESAI',
        'STATUS',
        'KD_UNIT',
    ];

    protected $casts = [
        'TGL_MULAI'   => 'date',
        'TGL_SELESAI' => 'date',
        'STATUS'      => 'boolean',
    ];

    protected $attributes = [
        'STATUS' => 1,
    ];

    /* =====================================================
     *  SPATIE ACTIVITY LOG (AUTO CRUD ONLY)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('sdt')
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
        $label = "SDT \"{$this->NAMA_SDT}\"";

        return match ($event) {
            'created' => "{$label} dibuat",
            'updated' => "{$label} diperbarui",
            'deleted' => "{$label} dihapus",
            default => "{$label} {$event}",
        };
    }

    /**
     * 🔥 FORCE EVENT = deleted
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
     *  GLOBAL SCOPE
     * ===================================================== */
    protected static function booted(): void
    {
        static::addGlobalScope('active', function (Builder $q) {
            $q->where('STATUS', 1);
        });

        static::creating(function (self $m) {
            if (is_null($m->STATUS)) {
                $m->STATUS = 1;
            }

            $m->NAMA_SDT = trim((string) $m->NAMA_SDT);
        });
    }

    /**
     * Ambil data termasuk STATUS = 0
     */
    public function scopeWithInactive(Builder $q): Builder
    {
        return $q->withoutGlobalScope('active');
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy($this->primaryKey, 'asc');
    }

    /* =====================================================
     *  RELASI
     * ===================================================== */

    /** Detail SDT */
    public function details()
    {
        return $this->hasMany(DtSdt::class, 'ID_SDT', 'ID');
    }

    /** Status penyampaian */
    public function statusPenyampaian()
    {
        return $this->hasMany(StatusPenyampaian::class, 'ID_SDT', 'ID');
    }

    /* =====================================================
     *  FLAG & ACCESSOR
     * ===================================================== */

    /**
     * SDT sudah pernah ada penyampaian
     * → tidak boleh dihapus
     */
    public function getSudahDisampaikanAttribute(): bool
    {
        return $this->statusPenyampaian()->exists();
    }

    /**
     * Nama petugas unik
     */
    public function getPetugasNamesAttribute(): Collection
    {
        $details = $this->relationLoaded('details')
            ? $this->details
            : $this->details()
            ->select('ID', 'ID_SDT', 'PETUGAS_SDT')
            ->get();

        return collect($details)
            ->pluck('PETUGAS_SDT')
            ->filter(fn($v) => filled($v))
            ->map(fn($v) => trim((string) $v))
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Jumlah detail SDT
     */
    public function getDetailsCountAttribute(): int
    {
        return $this->relationLoaded('details')
            ? $this->details->count()
            : $this->details()->count();
    }

    /* =====================================================
     *  HELPER METHODS
     * ===================================================== */

    /**
     * Soft delete SDT (logical)
     */
    public function softDelete(): bool
    {
        if ($this->sudah_disampaikan) {
            return false;
        }

        if ((int) $this->STATUS === 0) {
            return true;
        }

        $this->STATUS = 0;
        return $this->save();
    }

    /**
     * Restore SDT
     */
    public function restoreActive(): bool
    {
        if ((int) $this->STATUS === 1) {
            return true;
        }

        $this->STATUS = 1;
        return $this->save();
    }
}
