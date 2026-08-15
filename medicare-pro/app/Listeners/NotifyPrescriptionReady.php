<?php

namespace App\Listeners;

use App\Events\PrescriptionDispensed;
use App\Jobs\SendFirebaseNotification;
use App\Jobs\SendSmsNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * NotifyPrescriptionReady Listener
 *
 * Listens to the PrescriptionDispensed event and notifies the patient
 * that their prescription is ready for pickup from the pharmacy.
 * The notification includes the prescription ID and a link to view
 * the details in the app.
 */
class NotifyPrescriptionReady
{
    /**
     * Create the listener instance.
     */
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Handle the PrescriptionDispensed event.
     *
     * 1. Persists an in-app notification for the patient.
     * 2. Dispatches a Firebase push notification (async).
     * 3. Dispatches an SMS notification if the patient has a phone number (async).
     *
     * @param  PrescriptionDispensed  $event
     */
    public function handle(PrescriptionDispensed $event): void
    {
        $prescription = $event->prescription;
        $patient = $prescription->patient?->user;

        if (! $patient) {
            Log::warning('NotifyPrescriptionReady: No patient user found for prescription.', [
                'prescription_id' => $prescription->id,
                'patient_id' => $prescription->patient_id,
            ]);
            return;
        }

        $doctorName = $prescription->doctor?->user?->name ?? 'Your Doctor';
        $itemCount = $prescription->items?->count() ?? 0;

        $title = __('notifications.prescription_ready');
        $body = __(
            'notifications.prescription_ready_body',
            [
                'doctor' => $doctorName,
                'items' => $itemCount,
            ]
        );

        // 1. In-app database notification
        try {
            $this->notificationService->sendNotification(
                user: $patient,
                type: 'prescription_ready',
                title: $title,
                message: $body,
                data: [
                    'prescription_id' => $prescription->id,
                    'medical_record_id' => $prescription->medical_record_id,
                    'doctor_name' => $doctorName,
                    'item_count' => $itemCount,
                ],
            );
        } catch (\Throwable $e) {
            Log::error("NotifyPrescriptionReady: Failed to save in-app notification: {$e->getMessage()}");
        }

        // 2. Firebase push notification (async)
        if (! empty($patient->device_token)) {
            SendFirebaseNotification::dispatch(
                user_ids: [$patient->id],
                title: $title,
                body: $body,
                data: [
                    'type' => 'prescription_ready',
                    'prescription_id' => $prescription->id,
                    'screen' => 'prescription-details',
                ],
            );
        }

        // 3. SMS notification (async)
        if (! empty($patient->phone)) {
            $smsMessage = "MediCare Pro - Your prescription ({$itemCount} item(s)) from {$doctorName} is ready for pickup at the pharmacy.";

            SendSmsNotification::dispatch(
                phone_number: $patient->phone,
                message: $smsMessage,
            );
        }

        Log::info('NotifyPrescriptionReady: Patient notified that prescription is ready.', [
            'prescription_id' => $prescription->id,
            'patient_id' => $patient->id,
            'item_count' => $itemCount,
        ]);
    }
}
