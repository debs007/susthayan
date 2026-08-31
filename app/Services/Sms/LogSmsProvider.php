<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Log;

/**
 * Local/dev driver - writes the message to the log instead of sending a
 * real SMS. Set SMS_PROVIDER=log in .env while you don't have (or don't
 * want to spend) MSG91 credits during development.
 */
class LogSmsProvider implements SmsProviderInterface
{
    public function send(string $mobile, string $message): void
    {
        Log::info("[SMS to {$mobile}] {$message}");
    }
}
