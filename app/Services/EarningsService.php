<?php

namespace App\Services;

use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EarningsService
{
    /**
     * Müəllimin ümumi qazancı: yalnız real daxil olmuş ödənişlər × commission_rate.
     */
    public function totalForTeacher(Teacher $teacher, ?Carbon $from = null, ?Carbon $to = null): float
    {
        $query = \App\Models\Payment::query()
            ->whereHas('enrollment.group', fn ($q) => $q->where('teacher_id', $teacher->id));

        if ($from) {
            $query->whereDate('paid_at', '>=', $from->toDateString());
        }
        if ($to) {
            $query->whereDate('paid_at', '<=', $to->toDateString());
        }

        $totalPayments = (float) $query->sum('amount');

        return round($totalPayments * ((float) $teacher->commission_rate / 100), 2);
    }

    /**
     * Bütün müəllimlər üçün qazanc, dashboard üçün hazır kolleksiya.
     */
    public function summaryByTeacher(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        return Teacher::where('is_active', true)
            ->get()
            ->map(fn (Teacher $t) => [
                'teacher' => $t,
                'earnings' => $this->totalForTeacher($t, $from, $to),
            ])
            ->sortByDesc('earnings')
            ->values();
    }
}
