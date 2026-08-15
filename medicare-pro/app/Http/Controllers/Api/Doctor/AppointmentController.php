<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\MedicalRecordResource;
use App\Http\Resources\PrescriptionResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;
        $query = Appointment::where('doctor_id', $doctor->id)
            ->with(['patient.user', 'department', 'hospital', 'queueLog']);

        if ($request->has('date')) {
            $query->whereDate('appointment_date', $request->date);
        } else {
            $query->whereDate('appointment_date', today());
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_time')->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => AppointmentResource::collection($appointments),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ],
        ]);
    }

    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        return response()->json([
            'data' => new AppointmentResource($appointment->load(['patient.user', 'patient', 'department', 'medicalRecord', 'queueLog'])),
        ]);
    }

    public function start(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        $appointment->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json([
            'message' => __('appointments.consultation_started'),
            'data' => new AppointmentResource($appointment->fresh()),
        ]);
    }

    public function complete(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        $appointment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => __('appointments.consultation_completed'),
            'data' => new AppointmentResource($appointment->fresh()),
        ]);
    }
}
