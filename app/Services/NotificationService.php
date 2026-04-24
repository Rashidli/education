<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\PaymentNotification;
use App\Notifications\Channels\DatabaseChannel;
use App\Notifications\Channels\NotificationChannel;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class NotificationService
{
    /** @var array<string, NotificationChannel> */
    private array $channels;

    public function __construct()
    {
        $this->channels = [
            PaymentNotification::CHANNEL_DATABASE => new DatabaseChannel(),
            PaymentNotification::CHANNEL_SMS => new SmsChannel(),
            PaymentNotification::CHANNEL_WHATSAPP => new WhatsAppChannel(),
        ];
    }

    /**
     * Yaxınlaşan ödənişlər üçün notification yaradır (duplicate qoymur).
     */
    public function prepareUpcoming(int $windowDays): Collection
    {
        $today = Carbon::today();
        $until = $today->copy()->addDays($windowDays);

        $enrollments = Enrollment::query()
            ->with(['student', 'group.teacher'])
            ->where('is_active', true)
            ->whereNotNull('next_due_date')
            ->whereBetween('next_due_date', [$today->toDateString(), $until->toDateString()])
            ->get();

        return $enrollments->map(function (Enrollment $enrollment) use ($today) {
            $daysBefore = (int) $today->diffInDays($enrollment->next_due_date, false);

            return PaymentNotification::firstOrCreate(
                [
                    'enrollment_id' => $enrollment->id,
                    'channel' => PaymentNotification::CHANNEL_DATABASE,
                    'due_date' => $enrollment->next_due_date->toDateString(),
                ],
                [
                    'status' => PaymentNotification::STATUS_PENDING,
                    'days_before' => $daysBefore,
                    'payload' => $this->buildPayload($enrollment, $daysBefore),
                ],
            );
        });
    }

    public function dispatchPending(): int
    {
        $sent = 0;

        PaymentNotification::where('status', PaymentNotification::STATUS_PENDING)
            ->get()
            ->each(function (PaymentNotification $n) use (&$sent) {
                $channel = $this->channels[$n->channel] ?? null;
                if (! $channel) {
                    return;
                }
                try {
                    $channel->send($n);
                    if ($n->fresh()->status === PaymentNotification::STATUS_SENT) {
                        $sent++;
                    }
                } catch (\Throwable $e) {
                    $n->update([
                        'status' => PaymentNotification::STATUS_FAILED,
                        'error' => $e->getMessage(),
                    ]);
                }
            });

        return $sent;
    }

    protected function buildPayload(Enrollment $enrollment, int $daysBefore): string
    {
        return sprintf(
            '%s, %s qrupu üzrə ödəniş %d gün sonra (%s) tələb olunur. Məbləğ: %s AZN.',
            $enrollment->student->full_name,
            $enrollment->group->name,
            $daysBefore,
            $enrollment->next_due_date->format('d.m.Y'),
            number_format((float) $enrollment->group->monthly_price, 2),
        );
    }
}
