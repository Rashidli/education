<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\TeacherPayout;
use App\Services\TeacherPayoutService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TeacherPayoutController extends Controller
{
    public function __construct(private TeacherPayoutService $service) {}

    public function show(Teacher $teacher, Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        $balance = $this->service->balance($teacher);
        $periodBalance = $this->service->balance($teacher, $from, $to);
        $breakdown = $this->service->breakdown($teacher, $from, $to);
        $payouts = $teacher->payouts()
            ->with('creator')
            ->orderByDesc('paid_at')
            ->limit(50)
            ->get();

        return view('payouts.show', [
            'teacher' => $teacher,
            'balance' => $balance,
            'periodBalance' => $periodBalance,
            'breakdown' => $breakdown,
            'payouts' => $payouts,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function store(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'paid_at' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->service->recordPayout($teacher, $data);

        return redirect()->route('teachers.payouts', $teacher)
            ->with('success', 'Müəllimə ödəniş qeyd edildi. Balans yeniləndi.');
    }

    public function destroy(Teacher $teacher, TeacherPayout $payout)
    {
        abort_unless($payout->teacher_id === $teacher->id, 403);

        $payout->delete();

        return back()->with('success', 'Ödəniş silindi.');
    }

    private function dateRange(Request $request): array
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfMonth();

        return [$from, $to];
    }
}
