<?php

namespace App\Services;

use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function __construct(private EarningsService $earnings) {}

    /**
     * Aylıq maliyyə hesabatı — aylar üzrə daxilolma, müəllim komissiyası, xalis.
     */
    public function monthlyFinancial(Carbon $from, Carbon $to): Collection
    {
        $payments = Payment::query()
            ->with('enrollment.group.teacher')
            ->whereBetween('paid_at', [$from->toDateString(), $to->toDateString()])
            ->get();

        return $payments
            ->groupBy(fn (Payment $p) => $p->paid_at->format('Y-m'))
            ->map(function (Collection $monthPayments, string $monthKey) {
                $revenue = (float) $monthPayments->sum('amount');
                $commission = (float) $monthPayments->sum(function (Payment $p) {
                    $rate = (float) $p->enrollment->group->teacher->commission_rate;

                    return (float) $p->amount * ($rate / 100);
                });

                return [
                    'month' => Carbon::createFromFormat('Y-m', $monthKey)->startOfMonth(),
                    'payments_count' => $monthPayments->count(),
                    'revenue' => round($revenue, 2),
                    'teacher_commission' => round($commission, 2),
                    'net' => round($revenue - $commission, 2),
                ];
            })
            ->sortBy('month')
            ->values();
    }

    public function teacherEarnings(Carbon $from, Carbon $to): Collection
    {
        return Teacher::where('is_active', true)
            ->get()
            ->map(function (Teacher $teacher) use ($from, $to) {
                $payments = Payment::query()
                    ->whereHas('enrollment.group', fn ($q) => $q->where('teacher_id', $teacher->id))
                    ->whereBetween('paid_at', [$from->toDateString(), $to->toDateString()]);

                $revenue = (float) $payments->sum('amount');
                $earnings = $this->earnings->totalForTeacher($teacher, $from, $to);

                return [
                    'teacher' => $teacher,
                    'payments_count' => (int) $payments->count(),
                    'revenue' => round($revenue, 2),
                    'commission_rate' => (float) $teacher->commission_rate,
                    'earnings' => round($earnings, 2),
                ];
            })
            ->sortByDesc('earnings')
            ->values();
    }

    public function studentPayments(Carbon $from, Carbon $to, ?int $groupId = null): Collection
    {
        $query = Student::query()
            ->with([
                'enrollments.group.teacher',
                'enrollments.payments' => fn ($q) => $q->whereBetween('paid_at', [$from->toDateString(), $to->toDateString()]),
            ])
            ->where('is_active', true);

        if ($groupId) {
            $query->whereHas('enrollments', fn ($q) => $q->where('group_id', $groupId));
        }

        return $query->get()->map(function (Student $student) {
            $totalPaid = $student->enrollments->flatMap->payments->sum('amount');

            return [
                'student' => $student,
                'enrollments' => $student->enrollments,
                'total_paid' => round($totalPaid, 2),
            ];
        });
    }
}
