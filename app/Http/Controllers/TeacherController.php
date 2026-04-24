<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Services\EarningsService;
use App\Services\SettingsService;
use App\Services\TeacherPayoutService;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function __construct(
        private EarningsService $earnings,
        private TeacherPayoutService $payouts,
    ) {}

    public function index(Request $request)
    {
        $query = Teacher::query()->withCount('groups');

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        if ($type = $request->string('type')->value()) {
            $query->where('type', $type);
        }

        $teachers = $query->latest()->paginate(15)->withQueryString();

        return view('teachers.index', [
            'teachers' => $teachers,
            'search' => $search,
            'type' => $type,
        ]);
    }

    public function create()
    {
        return view('teachers.create', [
            'teacher' => new Teacher([
                'type' => Teacher::TYPE_LOCAL,
                'commission_rate' => SettingsService::defaultCommissionFor(Teacher::TYPE_LOCAL),
                'is_active' => true,
            ]),
            'defaultCommissions' => [
                Teacher::TYPE_LOCAL => SettingsService::defaultCommissionFor(Teacher::TYPE_LOCAL),
                Teacher::TYPE_FOREIGN => SettingsService::defaultCommissionFor(Teacher::TYPE_FOREIGN),
            ],
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        Teacher::create($request->validated());

        return redirect()->route('teachers.index')
            ->with('success', 'Müəllim uğurla əlavə olundu.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load([
            'groups' => fn ($q) => $q->latest(),
            'groups.activeEnrollments',
        ]);

        return view('teachers.show', [
            'teacher' => $teacher,
            'totalEarnings' => $this->earnings->totalForTeacher($teacher),
            'thisMonthEarnings' => $this->earnings->totalForTeacher(
                $teacher,
                now()->startOfMonth(),
                now()->endOfMonth(),
            ),
            'balance' => $this->payouts->balance($teacher),
        ]);
    }

    public function edit(Teacher $teacher)
    {
        return view('teachers.edit', [
            'teacher' => $teacher,
            'defaultCommissions' => [
                Teacher::TYPE_LOCAL => SettingsService::defaultCommissionFor(Teacher::TYPE_LOCAL),
                Teacher::TYPE_FOREIGN => SettingsService::defaultCommissionFor(Teacher::TYPE_FOREIGN),
            ],
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $teacher->update($request->validated());

        return redirect()->route('teachers.index')
            ->with('success', 'Müəllim yeniləndi.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->groups()->exists()) {
            return back()->with('error', 'Bu müəllimin qrupları var. Əvvəlcə qrupları silin və ya başqa müəllimə keçirin.');
        }

        $teacher->delete();

        return redirect()->route('teachers.index')
            ->with('success', 'Müəllim silindi.');
    }
}
