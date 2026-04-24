<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes, TracksDeletedBy;

    protected $fillable = [
        'full_name', 'phone', 'email', 'birth_date',
        'notes', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollments(): HasMany
    {
        return $this->enrollments()->where('is_active', true);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->full_name));
        $first = mb_substr($parts[0] ?? '', 0, 1);
        $last = mb_substr($parts[1] ?? '', 0, 1);

        return mb_strtoupper($first . $last);
    }
}
