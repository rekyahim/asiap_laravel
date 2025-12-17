<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Modul extends Model
{
    use LogsActivity;

    protected $table   = 'modul';
    public $timestamps = false;

    protected $fillable = [
        'nama_modul',
        'lokasi_modul',
        'tglpost',
        'status',
    ];

    protected $casts = [
        'status'  => 'boolean',
        'tglpost' => 'datetime',
    ];

    protected $attributes = [
        'status' => 1,
    ];

    /* =====================================================
     *  SPATIE ACTIVITY LOG (AUTO CRUD ONLY)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('modul')

        // 🔥 WAJIB agar event & diff muncul
            ->logOnly([
                'nama_modul',
                'lokasi_modul',
                'status',
            ])

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
        $label = "Modul \"{$this->nama_modul}\"";

        return match ($event) {
            'created' => "{$label} ditambahkan",
            'updated' => "{$label} diperbarui",
            'deleted' => "{$label} dihapus",
            default => "{$label} {$event}",
        };
    }

    /**
     * 🔥 FORCE EVENT = deleted
     * Jika logical delete (status = 0)
     */
    public function tapActivity(Activity $activity, string $eventName): void
    {
        if (
            $eventName === 'updated'
            && $this->isDirty('status')
            && (int) $this->status === 0
        ) {
            $activity->event       = 'deleted';
            $activity->description = $this->activityDescription('deleted');
        }
    }

    /* =====================================================
     *  RELASI
     * ===================================================== */

    public function hakAkses(): BelongsToMany
    {
        return $this->belongsToMany(
            HakAkses::class,
            'hakakses_modul',
            'MODUL_ID',
            'HAKAKSES_ID'
        )->withPivot(['STATUS', 'TGLPOST']);
    }

    public function activeHakAkses(): BelongsToMany
    {
        return $this->hakAkses()->wherePivot('STATUS', 1);
    }

    /* =====================================================
     *  MODEL EVENTS
     * ===================================================== */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (is_null($m->tglpost)) {
                $m->tglpost = now();
            }

            if (is_null($m->status)) {
                $m->status = 1;
            }

            $m->nama_modul = trim((string) $m->nama_modul);
        });
    }

    /* =====================================================
     *  HELPER METHODS
     * ===================================================== */

    /**
     * Soft delete modul (logical)
     */
    public function softDelete(): bool
    {
        if ((int) $this->status === 0) {
            return true;
        }

        $this->status = 0;
        return $this->save(); // 🔥 trigger deleted
    }

    /**
     * Restore modul
     */
    public function restoreActive(): bool
    {
        if ((int) $this->status === 1) {
            return true;
        }

        $this->status = 1;
        return $this->save(); // 🔥 trigger updated
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status ? 'Aktif' : 'Nonaktif';
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->status;
    }
}
