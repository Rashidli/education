<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\EnrollmentService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(SettingsSeeder::class);

        $teachers = $this->seedTeachers();
        $groups = $this->seedGroups($teachers);
        $students = Student::factory(25)->create();

        $this->seedEnrollmentsAndPayments($students, $groups);
    }

    private function seedTeachers(): \Illuminate\Support\Collection
    {
        return collect([
            Teacher::factory()->local()->create(['name' => 'Elmar Hüseynov', 'email' => 'elmar@education.az']),
            Teacher::factory()->local()->create(['name' => 'Nigar Məmmədova', 'email' => 'nigar@education.az']),
            Teacher::factory()->local()->create(['name' => 'Rəşad Quliyev', 'email' => 'rashad@education.az']),
            Teacher::factory()->foreign()->create(['name' => 'John Anderson', 'email' => 'john@education.az']),
            Teacher::factory()->foreign()->create(['name' => 'Sarah Williams', 'email' => 'sarah@education.az']),
        ]);
    }

    private function seedGroups(\Illuminate\Support\Collection $teachers): \Illuminate\Support\Collection
    {
        $defs = [
            ['Riyaziyyat — Hazırlıq', 0, 120],
            ['Fizika — Orta', 1, 110],
            ['İnformatika — Başlanğıc', 2, 90],
            ['Kimya — İrəliləmiş', 0, 130],
            ['İngilis dili — B1', 3, 220],
            ['İngilis dili — B2', 3, 240],
            ['Alman dili — A2', 4, 200],
            ['Alman dili — B1', 4, 230],
        ];

        $groups = collect();
        foreach ($defs as $def) {
            $groups->push(Group::factory()->create([
                'name' => $def[0],
                'teacher_id' => $teachers[$def[1]]->id,
                'monthly_price' => $def[2],
                'starts_on' => Carbon::today()->subMonths(3)->toDateString(),
            ]));
        }

        return $groups;
    }

    private function seedEnrollmentsAndPayments(
        \Illuminate\Support\Collection $students,
        \Illuminate\Support\Collection $groups,
    ): void {
        $enrollmentService = app(EnrollmentService::class);
        $paymentService = app(PaymentService::class);

        foreach ($students as $student) {
            $studentGroups = $groups->random(rand(1, 2));

            foreach ($studentGroups as $group) {
                $joinDate = Carbon::today()->subDays(rand(5, 120));
                $enrollment = $enrollmentService->enroll($student, $group, $joinDate);

                $this->seedPaymentsForEnrollment($enrollment, $paymentService, $joinDate);
            }
        }
    }

    private function seedPaymentsForEnrollment($enrollment, PaymentService $service, Carbon $joinDate): void
    {
        $today = Carbon::today();
        $group = $enrollment->group;

        $monthsPassed = max(0, (int) $joinDate->copy()->startOfMonth()->diffInMonths($today->copy()->startOfMonth()));

        if ($joinDate->day !== 1) {
            $service->record($enrollment->fresh(), [
                'amount' => (float) $enrollment->first_month_amount,
                'paid_at' => $joinDate->toDateString(),
                'period_month' => $joinDate->copy()->startOfMonth()->toDateString(),
                'method' => 'nağd',
            ]);
        } else {
            $service->record($enrollment->fresh(), [
                'amount' => (float) $group->monthly_price,
                'paid_at' => $joinDate->toDateString(),
                'period_month' => $joinDate->copy()->startOfMonth()->toDateString(),
                'method' => 'nağd',
            ]);
        }

        $skipLast = rand(0, 100) < 40;

        for ($i = 1; $i <= $monthsPassed; $i++) {
            if ($skipLast && $i === $monthsPassed) {
                break;
            }

            $periodMonth = $joinDate->copy()->startOfMonth()->addMonthsNoOverflow($i);
            $payDate = $periodMonth->copy()->addDays(rand(0, 5));

            if ($payDate->greaterThan($today)) {
                break;
            }

            $service->record($enrollment->fresh(), [
                'amount' => (float) $group->monthly_price,
                'paid_at' => $payDate->toDateString(),
                'period_month' => $periodMonth->toDateString(),
                'method' => ['nağd', 'kart', 'köçürmə'][rand(0, 2)],
            ]);
        }
    }
}
