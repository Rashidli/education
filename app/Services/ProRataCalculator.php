<?php

namespace App\Services;

use Carbon\CarbonInterface;

class ProRataCalculator
{
    /**
     * Ayın qalan günlərinə görə pro-rata məbləğ.
     *
     * Formula: monthlyPrice × (remainingDays / totalDaysInMonth)
     * Qoşulma günü daxil edilir: (daysInMonth - day + 1)
     */
    public function calculate(float $monthlyPrice, CarbonInterface $joinDate): float
    {
        $daysInMonth = $joinDate->daysInMonth;
        $remaining = $daysInMonth - $joinDate->day + 1;

        $amount = $monthlyPrice * ($remaining / $daysInMonth);

        return round($amount, 2);
    }

    /**
     * Tam ay (ayın 1-i) qoşulanlar üçün pro-rata tətbiq olunmur.
     */
    public function isFullMonth(CarbonInterface $joinDate): bool
    {
        return $joinDate->day === 1;
    }
}
