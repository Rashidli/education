<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Student;
use App\Services\EnrollmentService;
use App\Services\ProRataCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    public function __construct(private EnrollmentService $service) {}

    public function store(StoreEnrollmentRequest $request, Student $student)
    {
        $group = Group::findOrFail($request->integer('group_id'));
        $joinDate = Carbon::parse($request->input('joined_at'));

        $this->service->enroll($student, $group, $joinDate);

        return redirect()->route('students.show', $student)
            ->with('success', sprintf(
                '%s qrupuna qoşuldu. İlk ay pro-rata məbləği hesablandı.',
                $group->name,
            ));
    }

    public function preview(Request $request, ProRataCalculator $proRata)
    {
        $group = Group::findOrFail($request->integer('group_id'));
        $joinDate = Carbon::parse($request->input('joined_at', now()->toDateString()));

        $amount = $proRata->isFullMonth($joinDate)
            ? (float) $group->monthly_price
            : $proRata->calculate((float) $group->monthly_price, $joinDate);

        return response()->json([
            'amount' => $amount,
            'is_prorata' => ! $proRata->isFullMonth($joinDate),
            'monthly_price' => (float) $group->monthly_price,
            'days_in_month' => $joinDate->daysInMonth,
            'remaining_days' => $joinDate->daysInMonth - $joinDate->day + 1,
        ]);
    }

    public function destroy(Student $student, Enrollment $enrollment)
    {
        abort_unless($enrollment->student_id === $student->id, 403);

        $this->service->deactivate($enrollment);

        return back()->with('success', 'Tələbə qrupdan çıxarıldı.');
    }
}
