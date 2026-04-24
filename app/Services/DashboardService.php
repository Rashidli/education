<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(private EarningsService $earnings) {}

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
}
