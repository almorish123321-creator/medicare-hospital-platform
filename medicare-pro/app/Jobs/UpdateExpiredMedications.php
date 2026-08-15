<?php

namespace App\Jobs;

use App\Models\Hospital;
use App\Models\Medication;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * UpdateExpiredMedications Job
 *
 * Scans every hospital's medication inventory and updates the status
 * of medications whose expiry date has passed. Also detects medications
 * that will expire within the next 30 days and notifies pharmacy staff.
 *
 * This job is intended to run on a daily schedule (e.g. via the task
 * scheduler) to keep inventory statuses accurate.
 */
class UpdateExpiredMedications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 300;

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
     * 1. Finds all medications whose expiry_date is in the past but
     *    whose status is not yet 'expired' and updates them.
     * 2. Finds medications expiring within the next 30 days and
     *    notifies the hospital's pharmacist / admin.
     * 3. Also recalculates stock-based statuses (out_of_stock, low_stock).
     */
    public function handle(NotificationService $notificationService): void
    {
        try {
            $this->markExpiredMedications();
            $this->notifyExpiringSoonMedications($notificationService);
            $this->recalculateStockStatuses();

            Log::info('UpdateExpiredMedications: Daily medication status update completed.');
        } catch (\Throwable $e) {
            Log::error("UpdateExpiredMedications: Job failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Mark all medications whose expiry date has passed as 'expired'.
     */
    protected function markExpiredMedications(): int
    {
        $expiredCount = 0;

        // Query medications where the expiry date is past but status isn't expired yet
        $medications = Medication::where('expiry_date', '<', now()->toDateString())
            ->where('status', '!=', 'expired')
            ->get();

        foreach ($medications as $medication) {
            $previousStatus = $medication->status;
            $medication->updateStatus();
            $expiredCount++;

            if ($medication->wasChanged('status')) {
                Log::info('UpdateExpiredMedications: Medication marked as expired.', [
                    'medication_id' => $medication->id,
                    'name' => $medication->name,
                    'hospital_id' => $medication->hospital_id,
                    'previous_status' => $previousStatus,
                ]);
            }
        }

        if ($expiredCount > 0) {
            Log::info("UpdateExpiredMedications: Marked {$expiredCount} medication(s) as expired.");
        }

        return $expiredCount;
    }

    /**
     * Notify pharmacy staff about medications expiring within 30 days.
     */
    protected function notifyExpiringSoonMedications(NotificationService $notificationService): void
    {
        $expiringSoon = Medication::with('hospital')
            ->expiringSoon(30)
            ->where('status', 'available')
            ->get()
            ->groupBy('hospital_id');

        foreach ($expiringSoon as $hospitalId => $medications) {
            $hospital = Hospital::find($hospitalId);
            if (! $hospital) {
                continue;
            }

            // Find pharmacists or hospital admins to notify
            $staff = \App\Models\User::where('hospital_id', $hospitalId)
                ->where('status', 'active')
                ->whereIn('role', ['pharmacist', 'hospital_admin'])
                ->get();

            $medicationNames = $medications->pluck('name')->join(', ');

            foreach ($staff as $user) {
                $notificationService->sendNotification(
                    $user,
                    'medication_expiry_warning',
                    __('notifications.medications_expiring_soon'),
                    "{$medications->count()} medication(s) expiring within 30 days: {$medicationNames}",
                    ['hospital_id' => $hospitalId, 'count' => $medications->count()]
                );
            }
        }
    }

    /**
     * Recalculate stock-based statuses for all medications.
     *
     * Medications with zero stock are marked 'out_of_stock',
     * and those with stock <= 10 are marked 'low_stock'.
     */
    protected function recalculateStockStatuses(): int
    {
        $updatedCount = 0;

        // Out of stock
        $outOfStock = Medication::where('stock_quantity', 0)
            ->where('status', '!=', 'expired')
            ->where('status', '!=', 'out_of_stock')
            ->get();

        foreach ($outOfStock as $medication) {
            $medication->updateStatus();
            $updatedCount++;
        }

        // Low stock
        $lowStock = Medication::where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->where('status', 'available')
            ->get();

        foreach ($lowStock as $medication) {
            $medication->updateStatus();
            $updatedCount++;
        }

        if ($updatedCount > 0) {
            Log::info("UpdateExpiredMedications: Updated stock status for {$updatedCount} medication(s).");
        }

        return $updatedCount;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('UpdateExpiredMedications: Job failed permanently.', [
            'error' => $exception->getMessage(),
        ]);
    }
}
