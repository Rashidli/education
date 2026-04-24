<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use App\Services\SettingsService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('payments:notify-upcoming {--window= : Neçə gün əvvəl xəbərdarlıq}')]
#[Description('Yaxınlaşan ödənişlər üçün notification yaradır və pending olanları göndərir')]
class NotifyUpcomingPayments extends Command
{
    public function handle(NotificationService $service): int
    {
        $window = (int) ($this->option('window') ?? SettingsService::upcomingWindowDays());

        $created = $service->prepareUpcoming($window);
        $this->info("Hazırlandı: {$created->count()} notification ({$window} gün pəncərəsi)");

        $sent = $service->dispatchPending();
        $this->info("Göndərildi: {$sent}");

        return self::SUCCESS;
    }
}
