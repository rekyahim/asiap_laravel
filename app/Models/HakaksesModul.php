<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class HakaksesModul extends Model
{
    use LogsActivity;

    protected $table      = 'hakakses_modul';
    protected $primaryKey = 'ID';
    public $incrementing  = true;
    public $timestamps    = false;

    protected $fillable = [
        'HAKAKSES_ID',
        'MODUL_ID',
        'STATUS',
        'TGLPOST',
    ];

    protected $casts = [
        'STATUS'  => 'boolean',
        'TGLPOST' => 'datetime',
    ];

    protected $attributes = [
        'STATUS' => 1,
    ];

    /* =====================================================
     *  SPATIE ACTIVITY LOG (AUTO CRUD)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hakakses_modul')

        // 🔥 WAJIB agar old/new jelas
            ->logOnly([
                'HAKAKSES_ID',
                'MODUL_ID',
                'STATUS',
            ])

            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()

            ->setDescriptionForEvent(fn(string $event) =>
                $this->activityDescription($event)
            );
    }

    /**
     * Deskripsi log yang jelas & audit-friendly
     */
    protected function activityDescription(string $event): string
    {
        $hak   = $this->hakAkses?->HAKAKSES ?? 'Hak Akses';
        $modul = $this->modul?->nama_modul ?? 'Modul';

        $label = "Akses Modul \"{$modul}\" untuk {$hak}";

        return match ($event) {
            'created' => "{$label} ditambahkan",
            'updated' => "{$label} diperbarui",
            'deleted' => "{$label} dihapus",
            default => "{$label} {$event}",
        };
    }

    /**
     * 🔥 FORCE EVENT = deleted
     * Jika soft delete (STATUS = 0)
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
     *  RELASI
     * ===================================================== */

    public function hakAkses(): BelongsTo
    {
        return $this->belongsTo(HakAkses::class, 'HAKAKSES_ID', 'ID');
    }

    public function modul(): BelongsTo
    {
        return $this->belongsTo(Modul::class, 'MODUL_ID', 'ID');
    }

    /* =====================================================
     *  QUERY SCOPES
     * ===================================================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('STATUS', 1);
    }

    /* =====================================================
     *  MODEL EVENTS
     * ===================================================== */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (is_null($m->TGLPOST)) {
                $m->TGLPOST = now();
            }

            if (is_null($m->STATUS)) {
                $m->STATUS = 1;
            }
        });
    }

    /* =====================================================
     *  HELPER METHODS
     * ===================================================== */

    /**
     * Soft delete akses modul
     */
    public function softDelete(): bool
    {
        if ((int) $this->STATUS === 0) {
            return true;
        }

        $this->STATUS = 0;
        return $this->save(); // 🔥 trigger deleted
    }

    /**
     * Restore akses modul
     */
    public function restoreActive(): bool
    {
        if ((int) $this->STATUS === 1) {
            return true;
        }

        $this->STATUS = 1;
        return $this->save(); // 🔥 trigger updated
    }
}
