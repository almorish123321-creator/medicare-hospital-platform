<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalRecordResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'vital_signs' => 'nullable|array',
            'vital_signs.blood_pressure' => 'nullable|string',
            'vital_signs.temperature' => 'nullable|numeric',
            'vital_signs.weight' => 'nullable|numeric',
            'vital_signs.height' => 'nullable|numeric',
            'vital_signs.heart_rate' => 'nullable|integer',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::find($validated['appointment_id']);

        if ($appointment->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        $record = MedicalRecord::updateOrCreate(
            ['appointment_id' => $validated['appointment_id']],
            [
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $request->user()->doctor->id,
                'vital_signs' => $validated['vital_signs'] ?? null,
                'symptoms' => $validated['symptoms'] ?? null,
                'diagnosis' => $validated['diagnosis'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'message' => __('medical.record_created'),
            'data' => new MedicalRecordResource($record->load(['doctor.user', 'patient', 'appointment'])),
        ], 201);
    }

    public function update(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        if ($medicalRecord->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validate([
            'vital_signs' => 'nullable|array',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $medicalRecord->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => __('medical.medical_record')]),
            'data' => new MedicalRecordResource($medicalRecord->fresh()),
        ]);
    }
}
