<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function status(Request $request): JsonResponse
    {
        $appointment = Appointment::where('patient_id', $request->user()->patient->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress'])
            ->whereDate('appointment_date', today())
            ->with(['department', 'doctor.user', 'hospital', 'queueLog'])
            ->first();

        if (!$appointment) {
            return response()->json([
                'message' => __('appointments.no_appointments'),
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'appointment' => new AppointmentResource($appointment),
                'queue_number' => $appointment->getQueueDisplayNumber(),
                'queue_position' => $this->getQueuePosition($appointment),
                'estimated_wait' => $appointment->queueLog?->estimated_wait_time,
            ],
        ]);
    }

    private function getQueuePosition(Appointment $appointment): ?int
    {
        if (!$appointment->queue_number) return null;

        return Appointment::where('department_id', $appointment->department_id)
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['checked_in', 'pending'])
            ->whereNotNull('queue_number')
            ->where('queue_number', '<', $appointment->queue_number)
            ->count() + 1;
    }
}
