<?php

namespace App\Jobs;

use App\Services\QueueService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * ResetDailyQueues Job
 *
 * Clears all daily queue counter cache keys so that the next
 * patient check-in on a new day starts from queue number 1
 * per department. This job is scheduled to run at midnight
 * (or shortly after) via the task scheduler.
 *
 * The implementation clears cached keys that match the pattern
 * "queue_counter_{department_id}_{date}". Since those keys are
 * already set to expire at end-of-day, this job acts as a safety
 * net to ensure no stale counters leak into the next day.
 */
class ResetDailyQueues implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->onQueue('maintenance');
    }

    /**
     * Execute the job.
     *
     * Iterates over all active departments and clears any queue
     * counter cache keys that belong to the previous day or earlier.
     */
    public function handle(QueueService $queueService): void
    {
        try {
            $yesterday = now()->subDay()->toDateString();

            // Use the cache store's prefix to scan for queue counter keys
            $cachePrefix = config('cache.prefix', 'laravel_cache');
            $today = now()->toDateString();
            $resetCount = 0;

            // Clear yesterday's counter keys for all known departments
            $departments = \App\Models\Department::all('id');

            foreach ($departments as $department) {
                $yesterdayKey = "queue_counter_{$department->id}_{$yesterday}";

                if (Cache::forget($yesterdayKey)) {
                    $resetCount++;
                    Log::info("ResetDailyQueues: Cleared queue counter for department #{$department->id} ({$yesterdayKey}).");
                }

                // Also clear today's key if it somehow exists (e.g. early job run)
                $todayKey = "queue_counter_{$department->id}_{$today}";
                Cache::forget($todayKey);
            }

            // Delegate any additional reset logic to the service
            $queueService->resetDailyQueues();

            Log::info("ResetDailyQueues: Completed. Reset {$resetCount} cached counter(s) across {$departments->count()} department(s).");
        } catch (\Throwable $e) {
            Log::error("ResetDailyQueues: Failed to reset daily queues: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ResetDailyQueues: Job failed.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
