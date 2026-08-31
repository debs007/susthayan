<?php

namespace App\Services\Sms;

interface SmsProviderInterface
{
    /**
     * @throws \RuntimeException if the provider rejects or fails to send the message
     */
    public function send(string $mobile, string $message): void;
}
