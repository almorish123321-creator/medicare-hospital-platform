<?php

namespace App\Console\Commands;

use App\Jobs\CleanOldNotifications as CleanOldNotificationsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean {--days=30 : Number of days to retain read notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old read notifications';

    /**
     * Execute the console command.
     *
     * Dispatches the CleanOldNotifications job with the configured
     * retention period. Read notifications older than the specified
     * number of days are permanently deleted in chunked batches
     * to avoid database pressure.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');

        if ($days < 1) {
            $this->error('The --days option must be at least 1.');
            return self::FAILURE;
        }

        $this->info("Dispatching CleanOldNotifications job (retention: {$days} days)...");

        try {
            CleanOldNotificationsJob::dispatch($days);

            $this->info("CleanOldNotifications job dispatched successfully to the maintenance queue.");
            $this->newLine();
            $this->line("  Retention period : {$days} day(s)");
            $this->line("  Cutoff date      : " . now()->subDays($days)->toDateTimeString());
            $this->newLine();

            Log::info('CleanOldNotifications command: Job dispatched.', [
                'retention_days' => $days,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to dispatch CleanOldNotifications job: {$e->getMessage()}");
            Log::error('CleanOldNotifications command: Failed to dispatch job.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
