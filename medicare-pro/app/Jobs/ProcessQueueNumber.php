<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\QueueService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * ProcessQueueNumber Job
 *
 * Asynchronously assigns a queue number to a patient checking in.
 * This job is dispatched when a patient checks in for their appointment,
 * ensuring the queue assignment happens off the main request cycle
 * to keep response times fast and prevent race conditions on queue counters.
 *
 * @property int $appointment_id  The ID of the appointment being checked in.
 * @property int $hospital_id     The hospital where the check-in occurs.
 * @property int $department_id   The department the patient is visiting.
 */
class ProcessQueueNumber implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 30;

    /**
     * Create a new job instance.
     *
     * @param  int  $appointment_id  The appointment to assign a queue number for.
     * @param  int  $hospital_id     The hospital context for logging/scoping.
     * @param  int  $department_id   The department to generate the queue number in.
     */
    public function __construct(
        public int $appointment_id,
        public int $hospital_id,
        public int $department_id,
    ) {
        // Assign to the default queue; can be overridden at dispatch time
        $this->onQueue('queue-management');
    }

    /**
     * Execute the job.
     *
     * Retrieves the appointment, delegates queue number generation to
     * the QueueService, and persists the result. If the appointment has
     * already been assigned a queue number (idempotency guard), the job
     * exits early without side effects.
     */
    public function handle(QueueService $queueService): void
    {
        $appointment = Appointment::where('id', $this->appointment_id)
            ->where('hospital_id', $this->hospital_id)
            ->where('department_id', $this->department_id)
            ->first();

        if (! $appointment) {
            Log::warning("ProcessQueueNumber: Appointment #{$this->appointment_id} not found.");
            $this->fail("Appointment #{$this->appointment_id} not found.");
            return;
        }

        // Idempotency: skip if a queue number was already assigned
        if ($appointment->queue_number !== null) {
            Log::info("ProcessQueueNumber: Appointment #{$this->appointment_id} already has queue number #{$appointment->queue_number}. Skipping.");
            return;
        }

        try {
            $updatedAppointment = $queueService->assignQueueNumber($appointment);

            Log::info("ProcessQueueNumber: Assigned queue #{$updatedAppointment->queue_number} to appointment #{$this->appointment_id} in department #{$this->department_id}.");
        } catch (\Throwable $e) {
            Log::error("ProcessQueueNumber: Failed to assign queue number for appointment #{$this->appointment_id}: {$e->getMessage()}");
            $this->release(10);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessQueueNumber: Job failed for appointment #{$this->appointment_id}.", [
            'appointment_id' => $this->appointment_id,
            'hospital_id' => $this->hospital_id,
            'department_id' => $this->department_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
