<?php

namespace App\Notifications\Channels;

use App\Models\PaymentNotification;

interface NotificationChannel
{
    public function name(): string;

    public function send(PaymentNotification $notification): void;
}
