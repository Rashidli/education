<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Soft delete zamanı kimin sildiyini `deleted_by` sütununda saxlayır,
 * restore zamanı isə sıfırlayır.
 *
 * Modeldə SoftDeletes trait və `deleted_by` sütunu olmalıdır.
 */
trait TracksDeletedBy
{
    protected static function bootTracksDeletedBy(): void
    {
        static::deleting(function ($model) {
            if (! $model->isForceDeleting() && auth()->check()) {
                $model->deleted_by = auth()->id();
                $model->saveQuietly();
            }
        });

        static::restoring(function ($model) {
            $model->deleted_by = null;
        });
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
    }
}
