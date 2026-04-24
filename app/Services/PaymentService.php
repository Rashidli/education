<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Yeni ödəniş qeydə alır və enrollment-in next_due_date-ini yeniləyir.
     *
     * @param  array{amount: float, paid_at?: string|Carbon, period_month?: string|Carbon, method?: string, notes?: string}  $data
     */
    public function record(Enrollment $enrollment, array $data): Payment
    {
        return DB::transaction(function () use ($enrollment, $data) {
            $paidAt = isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : Carbon::today();
            $periodMonth = isset($data['period_month'])
                ? Carbon::parse($data['period_month'])->startOfMonth()
                : $this->guessPeriodMonth($enrollment);

            $isProrata = $this->isProrataPayment($enrollment, $periodMonth);

            $payment = Payment::create([
                'enrollment_id' => $enrollment->id,
                'amount' => $data['amount'],
                'paid_at' => $paidAt->toDateString(),
                'period_month' => $periodMonth->toDateString(),
                'is_prorata' => $isProrata,
                'method' => $data['method'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            $this->recalculateNextDueDate($enrollment);

            return $payment;
        });
    }

    /**
     * Ödənişin aid olduğu ayı təxmin edir — enrollment-in hələ ödənməmiş ən erkən ayı.
     */
    public function guessPeriodMonth(Enrollment $enrollment): Carbon
    {
        $lastPayment = $enrollment->payments()->orderByDesc('period_month')->first();

        if (! $lastPayment) {
            return Carbon::parse($enrollment->joined_at)->startOfMonth();
        }

        return Carbon::parse($lastPayment->period_month)->addMonthNoOverflow()->startOfMonth();
    }

    /**
     * İlk ay (qoşulma ayı) və tələbə ayın 1-i deyilsə — pro-rata sayılır.
     */
    protected function isProrataPayment(Enrollment $enrollment, Carbon $periodMonth): bool
    {
        $joinMonth = Carbon::parse($enrollment->joined_at)->startOfMonth();

        return $periodMonth->equalTo($joinMonth) && Carbon::parse($enrollment->joined_at)->day !== 1;
    }

    /**
     * next_due_date = sonuncu ödənişin paid_at + cycle_days.
     * Ödəniş yoxdursa joined_at + cycle_days.
     */
    public function recalculateNextDueDate(Enrollment $enrollment): void
    {
        $cycleDays = SettingsService::paymentCycleDays();
        $lastPayment = $enrollment->payments()->orderByDesc('paid_at')->first();

        $baseDate = $lastPayment
            ? Carbon::parse($lastPayment->paid_at)
            : Carbon::parse($enrollment->joined_at);

        $enrollment->update([
            'next_due_date' => $baseDate->copy()->addDays($cycleDays)->toDateString(),
        ]);
    }
}
