<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected string $apiKey;
    protected string $sender;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key', '');
        $this->sender = config('services.sms.sender', 'MediCarePro');
    }

    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::info("SMS (simulated) to {$phone}: {$message}");
            return true;
        }

        try {
            // Twilio or local SMS provider integration
            $response = Http::post(config('services.sms.endpoint'), [
                'api_key' => $this->apiKey,
                'sender' => $this->sender,
                'to' => $phone,
                'message' => $message,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    public function sendOtp(string $phone, string $otp): bool
    {
        $message = "Your MediCare Pro verification code is: {$otp}";
        return $this->send($phone, $message);
    }
}
