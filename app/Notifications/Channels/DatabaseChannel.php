<?php

namespace App\Notifications\Channels;

use App\Models\PaymentNotification;

class DatabaseChannel implements NotificationChannel
{
    public function name(): string
    {
        return PaymentNotification::CHANNEL_DATABASE;
    }

    public function send(PaymentNotification $notification): void
    {
        $notification->update([
            'status' => PaymentNotification::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }
}
