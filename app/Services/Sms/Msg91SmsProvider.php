<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class Msg91SmsProvider implements SmsProviderInterface
{
    public function __construct(
        private readonly string $authKey,
        private readonly string $senderId,
    ) {}

    public function send(string $mobile, string $message): void
    {
        // MSG91's v2 send-SMS shape as of when this was written - verify
        // field names against your own MSG91 dashboard before going live,
        // since accounts on the newer template "Flow" API need a template
        // ID instead of a raw `sms` array.
        //
        // IMPORTANT (India-specific): TRAI's DLT rules mean the *message*
        // text below generally has to match a template you've pre-registered
        // with MSG91/your telecom operator. An unregistered template isn't
        // an API error - it just gets silently dropped by the carrier. If
        // OTPs mysteriously never arrive in production, this is almost
        // always why. Register your OTP template before launch.
        $response = Http::withHeaders(['authkey' => $this->authKey])
            ->asJson()
            ->post('https://api.msg91.com/api/v2/sendsms', [
                'sender' => $this->senderId,
                'route' => '4',
                'country' => '91',
                'sms' => [
                    ['message' => $message, 'to' => [$this->toE164($mobile)]],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException("MSG91 SMS send failed: {$response->body()}");
        }
    }

    private function toE164(string $mobile): string
    {
        $digits = preg_replace('/\D/', '', $mobile);

        return str_starts_with($digits, '91') ? $digits : "91{$digits}";
    }
}
