<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StudentLedgerService
{
    public const STATUS_PAID = 'paid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_UPCOMING = 'upcoming';

    public function __construct(private ProRataCalculator $proRata) {}

    /**
     * Bir enrollment üçün ay-ay ledger.
     *
     * Qoşulma ayından bu günə qədər hər ay üçün gözlənilən və ödənilən məbləği müqayisə edir.
     *
     * @return array{
     *     periods: Collection<int, array{month: Carbon, expected: float, paid: float, status: string, is_prorata: bool, is_future: bool}>,
     *     total_expected: float,
     *     total_paid: float,
     *     balance: float,
     * }
     */
    public function forEnrollment(Enrollment $enrollment): array
    {
        $enrollment->loadMissing(['group', 'payments']);

        $joinDate = Carbon::parse($enrollment->joined_at);
        $today = Carbon::today();
        $monthlyPrice = (float) $enrollment->group->monthly_price;

        $paymentsByMonth = $enrollment->payments
            ->groupBy(fn ($p) => $p->period_month->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $periods = collect();
        $cursor = $joinDate->copy()->startOfMonth();
        $endCursor = $today->copy()->startOfMonth();

        while ($cursor->lessThanOrEqualTo($endCursor)) {
            $key = $cursor->format('Y-m');
            $isJoinMonth = $cursor->isSameMonth($joinDate);

            $expected = $isJoinMonth && $joinDate->day !== 1
                ? $this->proRata->calculate($monthlyPrice, $joinDate)
                : $monthlyPrice;

            $paid = (float) ($paymentsByMonth[$key] ?? 0);
            $isFuture = $cursor->greaterThan($today);

            $status = $this->statusFor($expected, $paid, $isFuture);

            $periods->push([
                'month' => $cursor->copy(),
                'expected' => round($expected, 2),
                'paid' => round($paid, 2),
                'status' => $status,
                'is_prorata' => $isJoinMonth && $joinDate->day !== 1,
                'is_future' => $isFuture,
            ]);

            $cursor->addMonthNoOverflow();
        }

        $totalExpected = (float) $periods->sum('expected');
        $totalPaid = (float) $periods->sum('paid');

        return [
            'periods' => $periods,
            'total_expected' => round($totalExpected, 2),
            'total_paid' => round($totalPaid, 2),
            'balance' => round($totalExpected - $totalPaid, 2),
        ];
    }

    /**
     * Tələbənin bütün enrollmentlərinin cəmi balance-ı.
     */
    public function totalBalanceForStudent(Student $student): float
    {
        $balance = 0;
        foreach ($student->enrollments as $enrollment) {
            $ledger = $this->forEnrollment($enrollment);
            $balance += $ledger['balance'];
        }

        return round($balance, 2);
    }

    protected function statusFor(float $expected, float $paid, bool $isFuture): string
    {
        if ($isFuture) {
            return self::STATUS_UPCOMING;
        }

        if ($paid <= 0) {
            return self::STATUS_UNPAID;
        }

        if ($paid + 0.01 >= $expected) {
            return self::STATUS_PAID;
        }

        return self::STATUS_PARTIAL;
    }
}
