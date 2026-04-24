<?php

namespace App\Notifications\Channels;

use App\Models\PaymentNotification;

/**
 * WhatsApp kanalı — stub. Meta Business API və ya 3rd party servis
 * (Twilio, Whapi, WaSenderAPI) ilə inteqrasiya edəcəksiniz.
 */
class WhatsAppChannel implements NotificationChannel
{
    public function name(): string
    {
        return PaymentNotification::CHANNEL_WHATSAPP;
    }

    public function send(PaymentNotification $notification): void
    {
        // TODO: Real WhatsApp API çağırışı.

        $notification->update([
            'status' => PaymentNotification::STATUS_PENDING,
            'error' => 'WhatsApp provayderi konfiqurasiya olunmayıb.',
        ]);
    }
}
