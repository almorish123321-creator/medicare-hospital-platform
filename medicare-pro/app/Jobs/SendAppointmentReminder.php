<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendAppointmentReminder Job
 *
 * Sends appointment reminders to patients via push notification
 * and SMS. Designed to be dispatched by scheduled tasks that
 * look for upcoming appointments (e.g. within the next 2 hours).
 *
 * @property int $appointment_id  The appointment to send a reminder for.
 */
class SendAppointmentReminder implements ShouldQueue
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
     * Create a new job instance.
     *
     * @param  int  $appointment_id  The ID of the appointment to remind the patient about.
     */
    public function __construct(
        public int $appointment_id,
    ) {
        $this->onQueue('reminders');
    }

    /**
     * Execute the job.
     *
     * Validates that the appointment is still upcoming before sending.
     * Appointments that have been cancelled or completed are skipped.
     */
    public function handle(NotificationService $notificationService): void
    {
        $appointment = Appointment::with(['patient.user', 'doctor.user', 'department'])
            ->find($this->appointment_id);

        if (! $appointment) {
            Log::warning("SendAppointmentReminder: Appointment #{$this->appointment_id} not found.");
            return;
        }

        // Only remind for valid upcoming appointments
        if (! in_array($appointment->status, ['confirmed', 'pending'], true)) {
            Log::info("SendAppointmentReminder: Appointment #{$this->appointment_id} status is '{$appointment->status}'. Skipping reminder.");
            return;
        }

        // Double-check the appointment is actually in the future
        $appointmentDatetime = $appointment->appointment_date->setTimeFrom($appointment->appointment_time);

        if ($appointmentDatetime->isPast()) {
            Log::info("SendAppointmentReminder: Appointment #{$this->appointment_id} has already passed. Skipping reminder.");
            return;
        }

        try {
            $notificationService->notifyAppointmentReminder($appointment);

            Log::info('SendAppointmentReminder: Reminder sent successfully.', [
                'appointment_id' => $this->appointment_id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'appointment_date' => $appointment->appointment_date->toDateString(),
                'appointment_time' => $appointment->appointment_time->format('H:i'),
            ]);
        } catch (\Throwable $e) {
            Log::error("SendAppointmentReminder: Failed to send reminder for appointment #{$this->appointment_id}: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTimeInterface
    {
        // Don't retry beyond the appointment time
        $appointment = Appointment::find($this->appointment_id);

        if ($appointment) {
            return $appointment->appointment_date
                ->setTimeFrom($appointment->appointment_time)
                ->subHour();
        }

        return now()->addHours(2);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendAppointmentReminder: Job failed permanently.', [
            'appointment_id' => $this->appointment_id,
            'error' => $exception->getMessage(),
        ]);
    }
}
