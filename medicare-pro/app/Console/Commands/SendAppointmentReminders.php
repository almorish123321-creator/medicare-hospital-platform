<?php

namespace App\Console\Commands;

use App\Jobs\SendAppointmentReminder;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for tomorrow\'s appointments';

    /**
     * Execute the console command.
     *
     * Queries for all confirmed or pending appointments scheduled for
     * tomorrow, then dispatches a SendAppointmentReminder job for each one.
     * Each job handles its own validation (e.g. double-checking status and
     * sending via the configured notification channels).
     */
    public function handle(): int
    {
        $tomorrow = now()->addDay()->toDateString();

        $this->info("Looking for appointments on {$tomorrow}...");

        try {
            $appointments = Appointment::with(['patient.user', 'doctor.user', 'department'])
                ->whereDate('appointment_date', $tomorrow)
                ->whereIn('status', ['confirmed', 'pending'])
                ->get();

            if ($appointments->isEmpty()) {
                $this->info('No upcoming appointments found for tomorrow. Nothing to do.');
                Log::info('SendAppointmentReminders command: No appointments found for tomorrow.');

                return self::SUCCESS;
            }

            $dispatched = 0;

            foreach ($appointments as $appointment) {
                SendAppointmentReminder::dispatch($appointment->id);
                $dispatched++;

                $this->line(
                    "  Queued reminder for appointment #{$appointment->id} "
                    . "(Patient: {$appointment->patient?->user?->name}, "
                    . "Doctor: {$appointment->doctor?->user?->name}, "
                    . "Time: {$appointment->appointment_time->format('H:i')})"
                );
            }

            $this->info("Dispatched {$dispatched} reminder job(s) for tomorrow's appointments.");
            Log::info('SendAppointmentReminders command: Dispatched reminder jobs.', [
                'appointment_count' => $dispatched,
                'date' => $tomorrow,
            ]);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to send appointment reminders: {$e->getMessage()}");
            Log::error('SendAppointmentReminders command: Failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return self::FAILURE;
        }
    }
}
