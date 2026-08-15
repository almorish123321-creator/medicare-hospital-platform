<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function sendNotification(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null
    ): Notification {
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        // Send push notification (Firebase)
        try {
            if ($user->device_token) {
                $this->sendPushNotification($user, $title, $message, $data);
            }
        } catch (\Exception $e) {
            Log::warning("Push notification failed: " . $e->getMessage());
        }

        // Send SMS if critical
        try {
            if (in_array($type, ['appointment_reminder', 'queue_update']) && $user->phone) {
                $this->sendSms($user, $message);
            }
        } catch (\Exception $e) {
            Log::warning("SMS notification failed: " . $e->getMessage());
        }

        return $notification;
    }

    public function sendPushNotification(User $user, string $title, string $message, ?array $data = null): void
    {
        // Firebase FCM integration placeholder
        // In production, use Laravel FCM package
        Log::info("Push notification to user {$user->id}: {$title}");
    }

    public function sendSms(User $user, string $message): void
    {
        // Twilio integration placeholder
        Log::info("SMS to {$user->phone}: {$message}");
    }

    public function notifyAppointmentBooked($appointment): void
    {
        $patient = $appointment->patient->user;
        $doctor = $appointment->doctor->user;

        $this->sendNotification(
            $patient,
            'appointment_reminder',
            __('appointments.appointment_booked'),
            __('notifications.appointment_booked'),
            ['appointment_id' => $appointment->id]
        );

        $this->sendNotification(
            $doctor,
            'system',
            __('appointments.appointment_booked'),
            __('notifications.appointment_booked'),
            ['appointment_id' => $appointment->id]
        );
    }

    public function notifyAppointmentReminder($appointment): void
    {
        $patient = $appointment->patient->user;
        $this->sendNotification(
            $patient,
            'appointment_reminder',
            __('notifications.appointment_reminder'),
            __('notifications.appointment_reminder_msg'),
            ['appointment_id' => $appointment->id]
        );
    }

    public function notifyPatientCheckedIn($appointment): void
    {
        $doctor = $appointment->doctor->user;
        $this->sendNotification(
            $doctor,
            'system',
            __('notifications.patient_checked_in'),
            __('notifications.patient_checked_in'),
            ['appointment_id' => $appointment->id]
        );
    }

    public function notifyQueueCalled($appointment): void
    {
        $patient = $appointment->patient->user;
        $this->sendNotification(
            $patient,
            'queue_update',
            __('notifications.queue_called'),
            __('notifications.queue_called') . ' ' . $appointment->getQueueDisplayNumber(),
            ['appointment_id' => $appointment->id, 'queue_number' => $appointment->getQueueDisplayNumber()]
        );
    }

    public function notifyPrescriptionReady($prescription): void
    {
        $patient = $prescription->patient->user;
        $this->sendNotification(
            $patient,
            'prescription_ready',
            __('notifications.prescription_ready'),
            __('notifications.prescription_ready_msg'),
            ['prescription_id' => $prescription->id]
        );
    }

    public function notifyPaymentSuccess($invoice): void
    {
        $patient = $invoice->patient->user;
        $this->sendNotification(
            $patient,
            'payment_success',
            __('notifications.payment_success'),
            __('notifications.payment_success_msg') . ' - ' . $invoice->total_amount,
            ['invoice_id' => $invoice->id]
        );
    }

    public function notifyAppointmentCancelled($appointment): void
    {
        $patient = $appointment->patient->user;
        $doctor = $appointment->doctor->user;
        $message = __('notifications.appointment_cancelled');

        $this->sendNotification($patient, 'system', $message, $message, ['appointment_id' => $appointment->id]);
        $this->sendNotification($doctor, 'system', $message, $message, ['appointment_id' => $appointment->id]);
    }
}
