<?php

namespace App\Listeners;

use App\Events\PatientCheckedIn;
use App\Jobs\SendFirebaseNotification;
use App\Jobs\SendSmsNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * NotifyPatientCheckIn Listener
 *
 * Listens to the PatientCheckedIn event and sends the patient
 * their assigned queue number along with the estimated wait time.
 * Notifications are delivered via in-app record, push notification,
 * and SMS so the patient can track their position in real time.
 */
class NotifyPatientCheckIn
{
    /**
     * Create the listener instance.
     */
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Handle the PatientCheckedIn event.
     *
     * 1. Persists an in-app notification for the patient.
     * 2. Dispatches a Firebase push notification with the queue number.
     * 3. Dispatches an SMS with the queue number and estimated wait.
     *
     * @param  PatientCheckedIn  $event
     */
    public function handle(PatientCheckedIn $event): void
    {
        $appointment = $event->appointment;
        $patient = $appointment->patient?->user;

        if (! $patient) {
            Log::warning('NotifyPatientCheckIn: No patient user found for appointment.', [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
            ]);
            return;
        }

        $queueDisplayNumber = $appointment->getQueueDisplayNumber();
        $departmentName = $appointment->department?->name ?? 'Unknown Department';

        if ($queueDisplayNumber === null) {
            Log::warning('NotifyPatientCheckIn: Appointment has no queue number assigned yet.', [
                'appointment_id' => $appointment->id,
            ]);
            return;
        }

        // Retrieve estimated wait time from the queue log if available
        $estimatedWait = null;
        if ($appointment->queueLog) {
            $estimatedWait = $appointment->queueLog->estimated_wait_time;
        }

        $title = __('notifications.check_in_title');
        $waitInfo = $estimatedWait !== null ? " (approx. {$estimatedWait} min)" : '';
        $body = __(
            'notifications.check_in_body',
            [
                'queue_number' => $queueDisplayNumber,
                'department' => $departmentName,
                'wait' => $waitInfo,
            ]
        );

        // 1. In-app database notification
        try {
            $this->notificationService->sendNotification(
                user: $patient,
                type: 'patient_checked_in',
                title: $title,
                message: $body,
                data: [
                    'appointment_id' => $appointment->id,
                    'queue_number' => $queueDisplayNumber,
                    'department_id' => $appointment->department_id,
                    'estimated_wait_minutes' => $estimatedWait,
                ],
            );
        } catch (\Throwable $e) {
            Log::error("NotifyPatientCheckIn: Failed to save in-app notification: {$e->getMessage()}");
        }

        // 2. Firebase push notification (async)
        if (! empty($patient->device_token)) {
            SendFirebaseNotification::dispatch(
                user_ids: [$patient->id],
                title: $title,
                body: $body,
                data: [
                    'type' => 'patient_checked_in',
                    'appointment_id' => $appointment->id,
                    'queue_number' => $queueDisplayNumber,
                    'screen' => 'queue-status',
                ],
            );
        }

        // 3. SMS notification (async)
        if (! empty($patient->phone)) {
            $smsMessage = "MediCare Pro - Your queue number is {$queueDisplayNumber} at {$departmentName}.{$waitInfo} Please wait in the waiting area.";

            SendSmsNotification::dispatch(
                phone_number: $patient->phone,
                message: $smsMessage,
            );
        }

        Log::info('NotifyPatientCheckIn: Patient notified of check-in and queue number.', [
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'queue_number' => $queueDisplayNumber,
        ]);
    }
}
