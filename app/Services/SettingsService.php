<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    public const LOCAL_DEFAULT_PRICE = 'local_default_price';
    public const FOREIGN_DEFAULT_PRICE = 'foreign_default_price';
    public const LOCAL_DEFAULT_COMMISSION = 'local_default_commission';
    public const FOREIGN_DEFAULT_COMMISSION = 'foreign_default_commission';
    public const PAYMENT_CYCLE_DAYS = 'payment_cycle_days';
    public const UPCOMING_WINDOW_DAYS = 'upcoming_window_days';

    public static function defaultPriceFor(string $teacherType): float
    {
        return (float) Setting::get(
            $teacherType === 'foreign' ? self::FOREIGN_DEFAULT_PRICE : self::LOCAL_DEFAULT_PRICE,
            $teacherType === 'foreign' ? 200 : 100
        );
    }

    public static function defaultCommissionFor(string $teacherType): float
    {
        return (float) Setting::get(
            $teacherType === 'foreign' ? self::FOREIGN_DEFAULT_COMMISSION : self::LOCAL_DEFAULT_COMMISSION,
            $teacherType === 'foreign' ? 60 : 50
        );
    }

    public static function paymentCycleDays(): int
    {
        return (int) Setting::get(self::PAYMENT_CYCLE_DAYS, 30);
    }

    public static function upcomingWindowDays(): int
    {
        return (int) Setting::get(self::UPCOMING_WINDOW_DAYS, 5);
    }
}
