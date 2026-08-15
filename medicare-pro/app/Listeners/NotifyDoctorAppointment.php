<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Jobs\SendFirebaseNotification;
use App\Jobs\SendSmsNotification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

/**
 * NotifyDoctorAppointment Listener
 *
 * Listens to the AppointmentCreated event and sends a notification
 * to the assigned doctor informing them of the new appointment.
 * The notification is dispatched both in-app (database record) and
 * via push/SMS so the doctor is promptly aware.
 *
 * This listener complements SendAppointmentNotification (which
 * notifies the patient) by focusing on the doctor's notification.
 */
class NotifyDoctorAppointment
{
    /**
     * Create the listener instance.
     */
    public function __construct(
        protected NotificationService $notificationService,
    ) {}

    /**
     * Handle the AppointmentCreated event.
     *
     * 1. Persists an in-app notification for the doctor.
     * 2. Dispatches a Firebase push notification (async).
     * 3. Dispatches an SMS notification if the doctor has a phone number (async).
     *
     * @param  AppointmentCreated  $event
     */
    public function handle(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;
        $doctor = $appointment->doctor?->user;

        if (! $doctor) {
            Log::warning('NotifyDoctorAppointment: No doctor user found for appointment.', [
                'appointment_id' => $appointment->id,
                'doctor_id' => $appointment->doctor_id,
            ]);
            return;
        }

        $patientName = $appointment->patient?->user?->name ?? 'Unknown Patient';
        $departmentName = $appointment->department?->name ?? 'Unknown Department';
        $appointmentDate = $appointment->appointment_date->format('M d, Y');
        $appointmentTime = $appointment->appointment_time->format('H:i');

        $title = __('notifications.new_appointment_title');
        $body = __(
            'notifications.new_appointment_for_doctor',
            [
                'patient' => $patientName,
                'department' => $departmentName,
                'date' => $appointmentDate,
                'time' => $appointmentTime,
            ]
        );

        // 1. In-app database notification
        try {
            $this->notificationService->sendNotification(
                user: $doctor,
                type: 'appointment_created',
                title: $title,
                message: $body,
                data: [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                    'department_id' => $appointment->department_id,
                    'appointment_date' => $appointment->appointment_date->toDateString(),
                    'appointment_time' => $appointment->appointment_time->format('H:i'),
                ],
            );
        } catch (\Throwable $e) {
            Log::error("NotifyDoctorAppointment: Failed to save in-app notification: {$e->getMessage()}");
        }

        // 2. Firebase push notification (async)
        if (! empty($doctor->device_token)) {
            SendFirebaseNotification::dispatch(
                user_ids: [$doctor->id],
                title: $title,
                body: $body,
                data: [
                    'type' => 'appointment_created',
                    'appointment_id' => $appointment->id,
                    'screen' => 'appointment-details',
                ],
            );
        }

        // 3. SMS notification (async) — only if the doctor has a phone
        if (! empty($doctor->phone)) {
            SendSmsNotification::dispatch(
                phone_number: $doctor->phone,
                message: "MediCare Pro - {$body}",
            );
        }

        Log::info('NotifyDoctorAppointment: Doctor notified of new appointment.', [
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'doctor_name' => $doctor->name,
        ]);
    }
}
