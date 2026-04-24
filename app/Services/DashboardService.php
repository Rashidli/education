<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherPayout;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        private EarningsService $earnings,
        private StudentLedgerService $studentLedger,
        private TeacherPayoutService $teacherPayouts,
    ) {}

    public function activeStudentsCount(): int
    {
        return Student::where('is_active', true)->count();
    }

    public function activeGroupsCount(): int
    {
        return Group::where('is_active', true)->count();
    }

    public function monthlyRevenue(?Carbon $month = null): float
    {
        $month = $month ?? Carbon::now();

        return (float) Payment::query()
            ->whereBetween('paid_at', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString(),
            ])
            ->sum('amount');
    }

    public function totalTeacherEarningsThisMonth(): float
    {
        $summary = $this->earnings->summaryByTeacher(
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        );

        return (float) $summary->sum('earnings');
    }

    /**
     * Yaxınlaşan ödənişlər: next_due_date bu gün və növbəti N gün arasında (default 5).
     */
    public function upcomingPayments(?int $windowDays = null): Collection
    {
        $window = $windowDays ?? SettingsService::upcomingWindowDays();
        $today = Carbon::today();
        $until = $today->copy()->addDays($window);

        return Enrollment::query()
            ->with(['student', 'group.teacher'])
            ->where('is_active', true)
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [$today->toDateString(), $until->toDateString()])
            ->orderBy('next_due_date')
            ->get();
    }

    public function overdueEnrollments(): Collection
    {
        return Enrollment::query()
            ->with(['student', 'group.teacher'])
            ->where('is_active', true)
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<', Carbon::today()->toDateString())
            ->orderBy('next_due_date')
            ->get();
    }

    public function topGroups(int $limit = 5): Collection
    {
        return Group::query()
            ->withCount(['enrollments as students_count' => fn ($q) => $q->where('is_active', true)])
            ->orderByDesc('students_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Bütün aktiv tələbələrin cəmi borcu.
     */
    public function totalStudentDebt(): float
    {
        $total = 0;
        Student::where('is_active', true)
            ->with(['enrollments.group', 'enrollments.payments'])
            ->chunk(100, function ($chunk) use (&$total) {
                foreach ($chunk as $student) {
                    $total += $this->studentLedger->totalBalanceForStudent($student);
                }
            });

        return round($total, 2);
    }

    /**
     * Müəllimlərə ödənilməli cəmi qalıq (bütün tarix boyu).
     */
    public function totalTeacherPayable(): float
    {
        $total = 0;
        foreach (Teacher::where('is_active', true)->get() as $t) {
            $total += max(0, $this->teacherPayouts->balance($t)['balance']);
        }

        return round($total, 2);
    }

    /**
     * Dashboard üçün müəllim balans cədvəli: hər müəllim — qazanılıb, ödənilib, qalıq.
     */
    public function teacherBalances(): Collection
    {
        return Teacher::where('is_active', true)->get()
            ->map(fn (Teacher $t) => [
                'teacher' => $t,
                'balance' => $this->teacherPayouts->balance($t),
            ])
            ->sortByDesc(fn ($row) => $row['balance']['balance'])
            ->values();
    }
}
