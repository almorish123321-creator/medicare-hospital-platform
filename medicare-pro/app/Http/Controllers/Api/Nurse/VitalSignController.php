<?php

namespace App\Http\Controllers\Api\Nurse;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VitalSignController extends Controller
{
    public function appointments(Request $request): JsonResponse
    {
        $appointments = Appointment::where('hospital_id', $request->user()->hospital_id)
            ->whereDate('appointment_date', today())
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->whereDoesntHave('medicalRecord')
            ->with(['patient.user', 'doctor.user', 'department'])
            ->orderBy('appointment_time')
            ->get();

        return response()->json([
            'data' => AppointmentResource::collection($appointments),
        ]);
    }

    public function storeVitalSigns(Request $request, Appointment $appointment): JsonResponse
    {
        if ($appointment->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validate([
            'vital_signs' => 'required|array',
            'vital_signs.blood_pressure' => 'required|string|max:20',
            'vital_signs.temperature' => 'required|numeric',
            'vital_signs.weight' => 'required|numeric',
            'vital_signs.height' => 'required|numeric',
            'vital_signs.heart_rate' => 'required|integer',
            'vital_signs.oxygen_saturation' => 'nullable|numeric',
            'vital_signs.respiratory_rate' => 'nullable|integer',
            'symptoms' => 'nullable|string',
        ]);

        // Create or update medical record with vital signs
        $record = MedicalRecord::updateOrCreate(
            ['appointment_id' => $appointment->id],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $appointment->doctor_id,
                'vital_signs' => $validated['vital_signs'],
                'symptoms' => $validated['symptoms'] ?? null,
            ]
        );

        return response()->json([
            'message' => __('messages.vitals_recorded'),
            'data' => new \App\Http\Resources\MedicalRecordResource($record->load(['patient', 'doctor.user'])),
        ], 201);
    }

    public function showPatient(Request $request, $patientId): JsonResponse
    {
        $patient = \App\Models\Patient::where('id', $patientId)
            ->whereHas('user', fn($q) => $q->where('hospital_id', $request->user()->hospital_id))
            ->with(['user', 'medicalRecords.doctor.user', 'medicalRecords.appointment'])
            ->firstOrFail();

        return response()->json([
            'data' => new \App\Http\Resources\PatientResource($patient),
        ]);
    }
}
