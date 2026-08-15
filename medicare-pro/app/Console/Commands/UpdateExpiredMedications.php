<?php

namespace App\Console\Commands;

use App\Jobs\UpdateExpiredMedications as UpdateExpiredMedicationsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateExpiredMedications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'medications:update-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update status of expired medications';

    /**
     * Execute the console command.
     *
     * Dispatches the UpdateExpiredMedications job which scans every
     * hospital's medication inventory, marks expired items, notifies
     * staff about soon-to-expire medications, and recalculates
     * stock-based statuses.
     */
    public function handle(): int
    {
        $this->info('Dispatching UpdateExpiredMedications job...');

        try {
            UpdateExpiredMedicationsJob::dispatch();

            $this->info('UpdateExpiredMedications job dispatched successfully to the maintenance queue.');
            Log::info('UpdateExpiredMedications command: Job dispatched.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to dispatch UpdateExpiredMedications job: {$e->getMessage()}");
            Log::error('UpdateExpiredMedications command: Failed to dispatch job.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
