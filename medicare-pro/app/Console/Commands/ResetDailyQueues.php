<?php

namespace App\Console\Commands;

use App\Jobs\ResetDailyQueues as ResetDailyQueuesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetDailyQueues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queues:reset-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all daily queue counters at midnight';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Dispatching ResetDailyQueues job...');

        try {
            ResetDailyQueuesJob::dispatch();

            $this->info('ResetDailyQueues job dispatched successfully to the maintenance queue.');
            Log::info('ResetDailyQueues command: Job dispatched.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to dispatch ResetDailyQueues job: {$e->getMessage()}");
            Log::error("ResetDailyQueues command: Failed to dispatch job.", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
