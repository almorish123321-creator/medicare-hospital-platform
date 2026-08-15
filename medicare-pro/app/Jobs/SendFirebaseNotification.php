<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SendFirebaseNotification Job
 *
 * Dispatches Firebase Cloud Messaging (FCM) push notifications to one
 * or more users. It supports both single-device and multicast messaging
 * by resolving each user's FCM device token before sending.
 *
 * Intended to be dispatched from notification-related services or
 * listeners so that push delivery never blocks the HTTP request.
 *
 * @property array<int>    $user_ids  The recipient user IDs.
 * @property string        $title     The notification title displayed on the device.
 * @property string        $body      The notification body text.
 * @property array<string, mixed> $data  Optional key-value payload attached to the notification.
 */
class SendFirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     Create a new job instance.

     @param  array<int>              $user_ids  One or more user IDs to receive the push notification.
     @param  string                  $title     Notification title.
     @param  string                  $body      Notification body.
     @param  array<string, mixed>    $data      Extra data payload sent alongside the notification.
     */
    public function __construct(
        public array $user_ids,
        public string $title,
        public string $body,
        public array $data = [],
    ) {
        $this->onQueue('notifications');
    }

    /**
     * Execute the job.
     *
     * Resolves FCM device tokens for every user, batches them into
     * chunks of 500 (FCM multicast limit), and sends each batch via
     * the Firebase Messaging API.
     *
     * Users without a device token are silently skipped and logged.
     */
    public function handle(): void
    {
        $serverKey = config('services.fcm.server_key');
        $fcmUrl = config('services.fcm.url', 'https://fcm.googleapis.com/fcm/send');

        if (empty($serverKey)) {
            Log::info('SendFirebaseNotification: FCM server key not configured. Logging notification instead.', [
                'user_ids' => $this->user_ids,
                'title' => $this->title,
            ]);
            return;
        }

        $users = User::whereIn('id', $this->user_ids)->get();

        if ($users->isEmpty()) {
            Log::warning('SendFirebaseNotification: No users found for the provided IDs.', [
                'user_ids' => $this->user_ids,
            ]);
            return;
        }

        // Collect valid device tokens, skip users without one
        $tokens = $users
            ->filter(fn (User $user) => ! empty($user->device_token))
            ->pluck('device_token')
            ->values()
            ->all();

        $skippedCount = $users->count() - count($tokens);

        if ($skippedCount > 0) {
            Log::info("SendFirebaseNotification: Skipped {$skippedCount} user(s) with no device token.");
        }

        if (empty($tokens)) {
            Log::info('SendFirebaseNotification: No valid device tokens found. Nothing to send.');
            return;
        }

        // FCM supports up to 500 tokens per multicast message
        $tokenChunks = collect($tokens)->chunk(500);

        foreach ($tokenChunks as $chunk) {
            $this->sendBatch($fcmUrl, $serverKey, $chunk->values()->all());
        }

        Log::info('SendFirebaseNotification: Dispatched FCM notifications.', [
            'total_users' => $users->count(),
            'tokens_sent' => count($tokens),
            'batches' => $tokenChunks->count(),
        ]);
    }

    /**
     * Send a single batch of notifications via FCM multicast.
     *
     * @param  string        $fcmUrl     The FCM API endpoint.
     * @param  string        $serverKey  The Firebase server key for authentication.
     * @param  array<string> $tokens     Device tokens for this batch.
     */
    protected function sendBatch(string $fcmUrl, string $serverKey, array $tokens): void
    {
        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $this->title,
                'body' => $this->body,
                'sound' => 'default',
            ],
            'data' => $this->data,
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "key={$serverKey}",
                'Content-Type' => 'application/json',
            ])->post($fcmUrl, $payload);

            if ($response->failed()) {
                Log::warning('SendFirebaseNotification: FCM batch request failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("SendFirebaseNotification: Exception during FCM batch send: {$e->getMessage()}");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendFirebaseNotification: Job failed.', [
            'user_ids' => $this->user_ids,
            'title' => $this->title,
            'error' => $exception->getMessage(),
        ]);
    }
}
