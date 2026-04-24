<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Enrollment;
use App\Models\Group;
use App\Models\Payment;
use App\Models\Teacher;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function index(Request $request)
    {
        $query = Payment::query()
            ->with(['enrollment.student', 'enrollment.group.teacher']);

        if ($teacherId = $request->integer('teacher_id')) {
            $query->whereHas('enrollment.group', fn ($q) => $q->where('teacher_id', $teacherId));
        }

        if ($groupId = $request->integer('group_id')) {
            $query->whereHas('enrollment', fn ($q) => $q->where('group_id', $groupId));
        }

        if ($month = $request->string('month')->value()) {
            $query->where('period_month', Carbon::parse($month)->startOfMonth()->toDateString());
        }

        if ($from = $request->string('from')->value()) {
            $query->whereDate('paid_at', '>=', $from);
        }
        if ($to = $request->string('to')->value()) {
            $query->whereDate('paid_at', '<=', $to);
        }

        $total = (clone $query)->sum('amount');
        $payments = $query->latest()->paginate(25)->withQueryString();

        return view('payments.index', [
            'payments' => $payments,
            'teachers' => Teacher::where('is_active', true)->latest()->get(),
            'groups' => Group::where('is_active', true)->latest()->get(),
            'filters' => $request->only(['teacher_id', 'group_id', 'month', 'from', 'to']),
            'total' => $total,
        ]);
    }

    public function create(Request $request)
    {
        $enrollment = null;
        if ($enrollmentId = $request->integer('enrollment')) {
            $enrollment = Enrollment::with(['student', 'group.teacher'])->findOrFail($enrollmentId);
        }

        $suggestedPeriod = $enrollment
            ? $this->service->guessPeriodMonth($enrollment)
            : Carbon::now()->startOfMonth();

        $suggestedAmount = $this->suggestedAmount($enrollment, $suggestedPeriod);

        return view('payments.create', [
            'enrollment' => $enrollment,
            'enrollments' => Enrollment::with(['student', 'group'])
                ->where('is_active', true)
                ->latest()
                ->get(),
            'suggestedPeriod' => $suggestedPeriod,
            'suggestedAmount' => $suggestedAmount,
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $enrollment = Enrollment::findOrFail($request->integer('enrollment_id'));

        $this->service->record($enrollment, $request->validated());

        return redirect()->route('payments.index')
            ->with('success', 'Ödəniş qeyd olundu. Növbəti ödəniş tarixi yeniləndi.');
    }

    public function destroy(Payment $payment)
    {
        $enrollment = $payment->enrollment;
        $payment->delete();
        $this->service->recalculateNextDueDate($enrollment);

        return back()->with('success', 'Ödəniş silindi.');
    }

    private function suggestedAmount(?Enrollment $enrollment, Carbon $period): float
    {
        if (! $enrollment) {
            return 0;
        }

        $joinMonth = Carbon::parse($enrollment->joined_at)->startOfMonth();
        $isFirstMonth = $period->equalTo($joinMonth) && Carbon::parse($enrollment->joined_at)->day !== 1;

        return $isFirstMonth
            ? (float) $enrollment->first_month_amount
            : (float) $enrollment->group->monthly_price;
    }
}
