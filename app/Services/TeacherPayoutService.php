<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Teacher;
use App\Models\TeacherPayout;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TeacherPayoutService
{
    /**
     * Müəllimin ümumi balansı.
     *
     * - earned: bütün tarix boyu qazanılmış komissiya
     * - paid: ona ödənilmiş cəmi məbləğ
     * - balance: qalıq (earned - paid), müsbət = borcluyuq
     *
     * @return array{earned: float, paid: float, balance: float}
     */
    public function balance(Teacher $teacher, ?Carbon $from = null, ?Carbon $to = null): array
    {
        $rate = (float) $teacher->commission_rate / 100;

        $paymentsQuery = Payment::query()
            ->whereHas('enrollment.group', fn ($q) => $q->where('teacher_id', $teacher->id));

        $payoutsQuery = TeacherPayout::where('teacher_id', $teacher->id);

        if ($from) {
            $paymentsQuery->whereDate('paid_at', '>=', $from->toDateString());
            $payoutsQuery->whereDate('paid_at', '>=', $from->toDateString());
        }
        if ($to) {
            $paymentsQuery->whereDate('paid_at', '<=', $to->toDateString());
            $payoutsQuery->whereDate('paid_at', '<=', $to->toDateString());
        }

        $revenue = (float) $paymentsQuery->sum('amount');
        $earned = round($revenue * $rate, 2);
        $paid = (float) $payoutsQuery->sum('amount');

        return [
            'earned' => $earned,
            'paid' => round($paid, 2),
            'balance' => round($earned - $paid, 2),
        ];
    }

    /**
     * Tələbə-ödəniş səviyyəsində detal: hər ödənişdən müəllim payı.
     */
    public function breakdown(Teacher $teacher, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $rate = (float) $teacher->commission_rate / 100;

        $query = Payment::query()
            ->with(['enrollment.student', 'enrollment.group'])
            ->whereHas('enrollment.group', fn ($q) => $q->where('teacher_id', $teacher->id));

        if ($from) {
            $query->whereDate('paid_at', '>=', $from->toDateString());
        }
        if ($to) {
            $query->whereDate('paid_at', '<=', $to->toDateString());
        }

        return $query->orderByDesc('paid_at')->get()
            ->map(fn (Payment $p) => [
                'payment' => $p,
                'student' => $p->enrollment->student,
                'group' => $p->enrollment->group,
                'commission' => round((float) $p->amount * $rate, 2),
            ]);
    }

    /**
     * Müəllimə ödəniş qeydə alır.
     */
    public function recordPayout(Teacher $teacher, array $data): TeacherPayout
    {
        return TeacherPayout::create([
            'teacher_id' => $teacher->id,
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? Carbon::today()->toDateString(),
            'method' => $data['method'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }
}
