<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Services\NotificationService;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected QueueService $queueService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;
        $query = Appointment::where('hospital_id', $hospitalId)
            ->with(['patient.user', 'doctor.user', 'department', 'queueLog']);

        if ($request->boolean('today', true)) {
            $query->whereDate('appointment_date', today());
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
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

    public function checkIn(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        if (!in_array($appointment->status, ['confirmed', 'pending'])) {
            return response()->json(['message' => __('messages.error')], 422);
        }

        $appointment->update([
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);

        $this->queueService->assignQueueNumber($appointment);
        $this->notificationService->notifyPatientCheckedIn($appointment);

        return response()->json([
            'message' => __('messages.check_in_success'),
            'data' => new AppointmentResource($appointment->fresh('queueLog')),
        ]);
    }

    public function noShow(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        $appointment->update(['status' => 'no_show']);

        return response()->json([
            'message' => __('appointments.appointment_cancelled'),
            'data' => new AppointmentResource($appointment->fresh()),
        ]);
    }

    public function walkIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'department_id' => 'required|exists:departments,id',
            'symptoms' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'hospital_id' => $request->user()->hospital_id,
            'department_id' => $validated['department_id'],
            'appointment_date' => today(),
            'appointment_time' => now()->format('H:i'),
            'status' => 'checked_in',
            'type' => 'walk_in',
            'symptoms' => $validated['symptoms'] ?? null,
            'checked_in_at' => now(),
        ]);

        $this->queueService->assignQueueNumber($appointment);

        return response()->json([
            'message' => __('appointments.appointment_created'),
            'data' => new AppointmentResource($appointment->load(['patient.user', 'doctor.user', 'department', 'queueLog'])),
        ], 201);
    }
}
