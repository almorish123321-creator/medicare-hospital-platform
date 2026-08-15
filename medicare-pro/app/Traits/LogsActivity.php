<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function (Model $model) {
            ActivityLog::log('created', $model, null, $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $changes = $model->getChanges();
            $original = [];
            foreach (array_keys($changes) as $key) {
                $original[$key] = $model->getOriginal($key);
            }
            ActivityLog::log('updated', $model, $original, $changes);
        });

        static::deleted(function (Model $model) {
            ActivityLog::log('deleted', $model, $model->getAttributes(), null);
        });
    }
}
