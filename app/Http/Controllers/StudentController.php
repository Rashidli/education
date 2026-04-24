<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Group;
use App\Models\Student;
use App\Services\StudentLedgerService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private StudentLedgerService $ledger) {}

    public function index(Request $request)
    {
        $query = Student::query()
            ->withCount(['enrollments as active_enrollments_count' => fn ($q) => $q->where('is_active', true)]);

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(fn ($q) => $q
                ->where('full_name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($status = $request->string('status')->value()) {
            $query->where('is_active', $status === 'active');
        }

        $students = $query->latest()->paginate(20)->withQueryString();

        return view('students.index', [
            'students' => $students,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('students.create', [
            'student' => new Student(['is_active' => true]),
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $student = Student::create($request->validated());

        return redirect()->route('students.show', $student)
            ->with('success', 'Tələbə əlavə olundu. İndi qrupa qoşa bilərsiniz.');
    }

    public function show(Student $student)
    {
        $student->load([
            'enrollments' => fn ($q) => $q->latest(),
            'enrollments.group.teacher',
            'enrollments.payments' => fn ($q) => $q->latest(),
        ]);

        $ledgers = $student->enrollments->mapWithKeys(
            fn ($e) => [$e->id => $this->ledger->forEnrollment($e)]
        );

        return view('students.show', [
            'student' => $student,
            'availableGroups' => Group::where('is_active', true)
                ->whereNotIn('id', $student->enrollments->where('is_active', true)->pluck('group_id'))
                ->with('teacher')
                ->latest()
                ->get(),
            'hasAnyActiveGroups' => Group::where('is_active', true)->exists(),
            'ledgers' => $ledgers,
            'totalBalance' => round((float) $ledgers->sum('balance'), 2),
            'totalExpected' => round((float) $ledgers->sum('total_expected'), 2),
            'totalPaid' => round((float) $ledgers->sum('total_paid'), 2),
        ]);
    }

    public function edit(Student $student)
    {
        return view('students.edit', ['student' => $student]);
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());

        return redirect()->route('students.index')
            ->with('success', 'Tələbə yeniləndi.');
    }

    public function destroy(Student $student)
    {
        if ($student->enrollments()->whereHas('payments')->exists()) {
            return back()->with('error', 'Bu tələbənin ödəniş tarixçəsi var. Silmək əvəzinə passiv edin.');
        }

        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Tələbə silindi.');
    }
}
