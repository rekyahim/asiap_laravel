<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Pengguna extends Authenticatable
{
    use Notifiable, LogsActivity;

    protected $table      = 'pengguna';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $fillable = [
        'USERNAME',
        'PASSWORD',
        'INITIAL_PASSWORD',
        'NAMA',
        'HAKAKSES_ID',
        'HAKAKSES',
        'KD_UNIT',
        'STATUS',
        'NIP',
        'ID_FOTO',
        'TGLPOST',
        'NAMA_UNIT',
        'JABATAN',
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
            ->useLogName('pengguna')

        // 🔥 WAJIB agar event & diff muncul
            ->logOnly([
                'USERNAME',
                'NAMA',
                'HAKAKSES_ID',
                'KD_UNIT',
                'STATUS',
                'NIP',
                'NAMA_UNIT',
                'JABATAN',
            ])

            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()

        // 🔒 JANGAN LOG PASSWORD
            ->logExcept([
                'PASSWORD',
                'INITIAL_PASSWORD',
            ])

            ->setDescriptionForEvent(fn(string $event) =>
                $this->activityDescription($event)
            );
    }

    /**
     * Deskripsi log human readable
     */
    protected function activityDescription(string $event): string
    {
        $label = "Pengguna \"{$this->NAMA}\"";

        return match ($event) {
            'created' => "{$label} ditambahkan",
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
     *  RELASI
     * ===================================================== */

    public function hakakses()
    {
        return $this->belongsTo(HakAkses::class, 'HAKAKSES_ID', 'ID');
    }

    /* =====================================================
     *  AUTH OVERRIDE
     * ===================================================== */

    /**
     * Agar Auth Laravel mengenali kolom PASSWORD
     */
    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    /* =====================================================
     *  ACCESSOR
     * ===================================================== */

    /**
     * URL foto profil
     */
    public function getFotoUrlAttribute(): string
    {
        if ($this->ID_FOTO && Storage::exists('public/profile/' . $this->ID_FOTO)) {
            return asset('storage/profile/' . $this->ID_FOTO);
        }

        return asset('assets/images/profile/user-1.jpg');
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->STATUS ? 'Aktif' : 'Nonaktif';
    }

    public function getIsActiveAttribute(): bool
    {
        return (bool) $this->STATUS;
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

            $m->NAMA = trim((string) $m->NAMA);
        });

        static::deleting(function (self $pengguna) {
            if (
                $pengguna->ID_FOTO
                && Storage::exists('public/profile/' . $pengguna->ID_FOTO)
            ) {
                Storage::delete('public/profile/' . $pengguna->ID_FOTO);
            }
        });
    }

    /* =====================================================
     *  HELPER METHODS
     * ===================================================== */

    /**
     * Soft delete pengguna
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
     * Restore pengguna
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
