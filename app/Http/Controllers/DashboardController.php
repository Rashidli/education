<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\EarningsService;
use App\Services\SettingsService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct(
        private DashboardService $dashboard,
        private EarningsService $earnings,
    ) {}

    public function __invoke()
    {
        $now = Carbon::now();
        $prevMonth = $now->copy()->subMonthNoOverflow();

        $thisMonthRevenue = $this->dashboard->monthlyRevenue($now);
        $prevMonthRevenue = $this->dashboard->monthlyRevenue($prevMonth);

        return view('dashboard', [
            'activeStudents' => $this->dashboard->activeStudentsCount(),
            'activeGroups' => $this->dashboard->activeGroupsCount(),
            'monthlyRevenue' => $thisMonthRevenue,
            'revenueChange' => $this->percentChange($prevMonthRevenue, $thisMonthRevenue),
            'teacherEarnings' => $this->dashboard->totalTeacherEarningsThisMonth(),
            'upcomingPayments' => $this->dashboard->upcomingPayments(),
            'overdueCount' => $this->dashboard->overdueEnrollments()->count(),
            'topGroups' => $this->dashboard->topGroups(5),
            'teacherBalances' => $this->dashboard->teacherBalances(),
            'totalStudentDebt' => $this->dashboard->totalStudentDebt(),
            'totalTeacherPayable' => $this->dashboard->totalTeacherPayable(),
            'upcomingWindow' => SettingsService::upcomingWindowDays(),
        ]);
    }

    private function percentChange(float $from, float $to): ?float
    {
        if ($from == 0.0) {
            return $to > 0 ? 100 : null;
        }

        return round((($to - $from) / $from) * 100, 1);
    }
}
