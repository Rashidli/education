<?php

namespace App\Notifications\Channels;

use App\Models\PaymentNotification;

/**
 * SMS kanalı — stub. Real SMS provayderini (məs. Atomicsms, Twilio) burada
 * inteqrasiya edəcəksiniz. send() içində HTTP çağırışı edin və nəticəni
 * notification-a yazın.
 */
class SmsChannel implements NotificationChannel
{
    public function name(): string
    {
        return PaymentNotification::CHANNEL_SMS;
    }

    public function send(PaymentNotification $notification): void
    {
        // TODO: Real SMS API çağırışı. Məsələn:
        // Http::post('https://api.provider/send', [
        //     'to' => $notification->enrollment->student->phone,
        //     'text' => $notification->payload,
        // ]);

        $notification->update([
            'status' => PaymentNotification::STATUS_PENDING,
            'error' => 'SMS provayderi konfiqurasiya olunmayıb — config/services.php-ə əlavə edin.',
        ]);
    }
}
