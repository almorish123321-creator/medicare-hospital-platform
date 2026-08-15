<?php

namespace App\Jobs;

use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CleanOldNotifications Job
 *
 * Permanently deletes read notifications that are older than a
 * configurable number of days (default: 30). This keeps the
 * notifications table lean and prevents unbounded growth.
 *
 * The job uses a chunked delete strategy to avoid locking the
 * table for too long on large datasets.
 */
class CleanOldNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * The number of days after which read notifications are eligible for deletion.
     */
    private int $retentionDays;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $retentionDays  Override the default 30-day retention period.
     */
    public function __construct(?int $retentionDays = null)
    {
        $this->retentionDays = $retentionDays ?? config('medicare.notifications.retention_days', 30);
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job.
     *
     * Deletes read notifications whose read_at timestamp is older
     * than the retention period. Uses chunked deletes (1 000 rows
     * per batch) to minimise database pressure.
     */
    public function handle(): void
    {
        $cutoff = now()->subDays($this->retentionDays);

        try {
            $totalDeleted = 0;

            // Chunked delete to avoid long-running queries
            do {
                $deleted = DB::table('notifications')
                    ->where('is_read', true)
                    ->whereNotNull('read_at')
                    ->where('read_at', '<', $cutoff)
                    ->limit(1000)
                    ->delete();

                $totalDeleted += $deleted;
            } while ($deleted > 0);

            if ($totalDeleted > 0) {
                Log::info('CleanOldNotifications: Cleaned up old read notifications.', [
                    'deleted_count' => $totalDeleted,
                    'retention_days' => $this->retentionDays,
                    'cutoff' => $cutoff->toDateTimeString(),
                ]);
            } else {
                Log::info('CleanOldNotifications: No old notifications to clean.', [
                    'retention_days' => $this->retentionDays,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("CleanOldNotifications: Failed to clean notifications: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanOldNotifications: Job failed permanently.', [
            'retention_days' => $this->retentionDays,
            'error' => $exception->getMessage(),
        ]);
    }
}
