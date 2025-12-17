<?php
namespace App\Models\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;

trait LogsActivityDefault
{
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName($this->getTable())
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $event) =>
                $this->activityDescription($event)
            );
    }

    protected function activityDescription(string $event): string
    {
        $name = $this->getActivityName();

        return match ($event) {
            'created' => "{$name} dibuat",
            'updated' => "{$name} diperbarui",
            'deleted' => "{$name} dihapus",
            default => "{$name} {$event}",
        };
    }

    protected function getActivityName(): string
    {
        return class_basename($this);
    }

    /**
     * 🔥 FORCE DELETE EVENT
     * untuk kasus STATUS = 0 (logical delete)
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
}
