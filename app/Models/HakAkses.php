<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class HakAkses extends Model
{
    use LogsActivity;

    protected $table      = 'hak_akses';
    protected $primaryKey = 'ID';
    protected $keyType    = 'int';
    public $incrementing  = true;
    public $timestamps    = false;

    protected $fillable = [
        'HAKAKSES',
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
     *  SPATIE ACTIVITY LOG (AUTO CRUD ONLY)
     * ===================================================== */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('hak_akses')

        // 🔥 WAJIB biar old/new jelas
            ->logOnly(['HAKAKSES', 'STATUS'])

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
        $label = "Hak Akses \"{$this->HAKAKSES}\"";

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

    public function moduls(): BelongsToMany
    {
        return $this->belongsToMany(
            Modul::class,
            'hakakses_modul',
            'HAKAKSES_ID',
            'MODUL_ID'
        )->withPivot(['STATUS', 'TGLPOST']);
    }

    public function activeModuls(): BelongsToMany
    {
        return $this->moduls()->wherePivot('STATUS', 1);
    }

    public function users(): HasMany
    {
        return $this->hasMany(Pengguna::class, 'HAKAKSES_ID', 'ID');
    }

    /* =====================================================
     *  QUERY SCOPES
     * ===================================================== */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('STATUS', 1);
    }

    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return filled($term)
            ? $q->where('HAKAKSES', 'like', "%{$term}%")
            : $q;
    }

    /* =====================================================
     *  MODEL EVENTS
     * ===================================================== */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            $m->HAKAKSES = trim((string) $m->HAKAKSES);

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
     * Soft delete (logical)
     */
    public function softDelete(): bool
    {
        if ((int) $this->STATUS === 0) {
            return true;
        }

        $this->STATUS = 0;
        return $this->save(); // 🔥 ini akan trigger "deleted"
    }

    /**
     * Restore kembali ke aktif
     */
    public function restoreActive(): bool
    {
        if ((int) $this->STATUS === 1) {
            return true;
        }

        $this->STATUS = 1;
        return $this->save(); // 🔥 ini akan trigger "updated"
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->STATUS ? 'Aktif' : 'Nonaktif';
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->STATUS;
    }
}
