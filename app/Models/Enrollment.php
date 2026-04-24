<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletedBy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes, TracksDeletedBy;

    protected $fillable = [
        'student_id', 'group_id', 'joined_at', 'left_at',
        'next_due_date', 'first_month_amount', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'left_at' => 'date',
            'next_due_date' => 'date',
            'first_month_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function daysUntilDue(): ?int
    {
        if (! $this->next_due_date) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->next_due_date, false);
    }

    public function isOverdue(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast();
    }
}
