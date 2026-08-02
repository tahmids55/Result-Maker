<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * Reusable activity logging trait for all MarksCraft models.
 * Tracks who created, updated, or deleted a record plus
 * which attributes changed (old → new).
 */
trait LogsActivityTrait
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()                        // Log every attribute change
            ->logOnlyDirty()                  // Only record attributes that actually changed
            ->dontSubmitEmptyLogs()           // Skip if nothing changed
            ->setDescriptionForEvent(function (string $eventName) {
                $model = class_basename(static::class);
                $label = $this->getActivityLabel();
                return "{$model} \"{$label}\" was {$eventName}";
            });
    }

    /**
     * Return a human-readable label for this model instance.
     * Override in individual models if needed.
     */
    protected function getActivityLabel(): string
    {
        return $this->name ?? $this->id ?? 'unknown';
    }
}
