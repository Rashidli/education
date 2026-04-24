<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes, TracksDeletedBy;

    public const TYPE_LOCAL = 'local';
    public const TYPE_FOREIGN = 'foreign';

    protected $fillable = [
        'name', 'type', 'commission_rate',
        'phone', 'email', 'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    public function enrollments(): HasManyThrough
    {
        return $this->hasManyThrough(Enrollment::class, Group::class);
    }

    public function typeLabel(): string
    {
        return $this->type === self::TYPE_LOCAL ? 'Yerli' : 'Xarici';
    }
}
