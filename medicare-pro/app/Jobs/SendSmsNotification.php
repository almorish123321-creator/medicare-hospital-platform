<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendSmsNotification Job
 *
 * Sends an SMS message to a single phone number via Twilio (or the
 * configured SMS provider through SmsService). Dispatched asynchronously
 * so the calling code never waits for the third-party API response.
 *
 * @property string $phone_number  The recipient phone number in E.164 format.
 * @property string $message      The SMS body text.
 */
class SendSmsNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying after a failure.
     */
    public int $backoff = 15;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param  string  $phone_number  The recipient phone number (E.164 format recommended).
     * @param  string  $message       The message body to deliver.
     */
    public function __construct(
        public string $phone_number,
        public string $message,
    ) {
        $this->onQueue('sms');
    }

    /**
     * Execute the job.
     *
     * Validates the phone number format before delegating to SmsService.
     * If the number is invalid the job is released back to the queue
     * rather than failing permanently, allowing upstream fixes.
     */
    public function handle(SmsService $smsService): void
    {
        $sanitizedNumber = $this->sanitizePhoneNumber($this->phone_number);

        if (! $this->isValidPhoneNumber($sanitizedNumber)) {
            Log::warning('SendSmsNotification: Invalid phone number format.', [
                'phone_number' => $this->phone_number,
            ]);
            // Do not retry — the number won't fix itself
            return;
        }

        try {
            $sent = $smsService->send($sanitizedNumber, $this->message);

            if ($sent) {
                Log::info('SendSmsNotification: SMS sent successfully.', [
                    'phone_number' => $sanitizedNumber,
                ]);
            } else {
                Log::warning('SendSmsNotification: SmsService returned false.', [
                    'phone_number' => $sanitizedNumber,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("SendSmsNotification: Exception while sending SMS: {$e->getMessage()}", [
                'phone_number' => $sanitizedNumber,
            ]);

            // Release back to the queue for another attempt
            $this->release(15);
        }
    }

    /**
     * Sanitize the phone number by stripping non-digit characters
     * (except a leading +).
     */
    protected function sanitizePhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // If the number starts with 0 and no +, assume local format and prepend country code
        if (str_starts_with($phone, '0') && ! str_starts_with($phone, '+')) {
            $countryCode = config('services.sms.default_country_code', '+966');
            $phone = $countryCode . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Basic E.164 validation: must start with + and be 10-15 digits total.
     */
    protected function isValidPhoneNumber(string $phone): bool
    {
        if (! str_starts_with($phone, '+')) {
            return false;
        }

        $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
        $length = strlen($digitsOnly);

        return $length >= 10 && $length <= 15;
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendSmsNotification: Job failed permanently.', [
            'phone_number' => $this->phone_number,
            'error' => $exception->getMessage(),
        ]);
    }
}
