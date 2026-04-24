<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function __construct(private ProRataCalculator $proRata) {}

    /**
     * Tələbəni qrupa qoşur və ilk ay üçün pro-rata hesablamasını saxlayır.
     * next_due_date qoşulma tarixindən N gün sonraya təyin olunur (default 30).
     */
    public function enroll(Student $student, Group $group, Carbon $joinDate): Enrollment
    {
        return DB::transaction(function () use ($student, $group, $joinDate) {
            $firstMonthAmount = $this->proRata->isFullMonth($joinDate)
                ? (float) $group->monthly_price
                : $this->proRata->calculate((float) $group->monthly_price, $joinDate);

            return Enrollment::create([
                'student_id' => $student->id,
                'group_id' => $group->id,
                'joined_at' => $joinDate->toDateString(),
                'first_month_amount' => $firstMonthAmount,
                'next_due_date' => $joinDate->copy()->addDays(SettingsService::paymentCycleDays())->toDateString(),
                'is_active' => true,
            ]);
        });
    }

    public function deactivate(Enrollment $enrollment, ?Carbon $leaveDate = null): Enrollment
    {
        $enrollment->update([
            'is_active' => false,
            'left_at' => ($leaveDate ?? Carbon::today())->toDateString(),
        ]);

        return $enrollment->fresh();
    }
}
