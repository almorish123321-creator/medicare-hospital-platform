<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionResource;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function __construct(protected NotificationService $notificationService) {}

    public function index(Request $request): JsonResponse
    {
        $prescriptions = Prescription::where('doctor_id', $request->user()->doctor->id)
            ->with(['patient.user', 'items', 'medicalRecord.appointment'])
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => PrescriptionResource::collection($prescriptions),
            'meta' => [
                'current_page' => $prescriptions->currentPage(),
                'last_page' => $prescriptions->lastPage(),
                'per_page' => $prescriptions->perPage(),
                'total' => $prescriptions->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'diagnosis' => 'required|string',
            'instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_name' => 'required|string',
            'items.*.dosage' => 'required|string',
            'items.*.duration' => 'required|string',
            'items.*.instructions' => 'nullable|string',
        ]);

        $medicalRecord = MedicalRecord::find($validated['medical_record_id']);

        if ($medicalRecord->doctor_id !== $request->user()->doctor->id) {
            abort(403, __('messages.forbidden'));
        }

        $prescription = Prescription::create([
            'medical_record_id' => $validated['medical_record_id'],
            'doctor_id' => $request->user()->doctor->id,
            'patient_id' => $medicalRecord->patient_id,
            'diagnosis' => $validated['diagnosis'],
            'instructions' => $validated['instructions'] ?? null,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            PrescriptionItem::create([
                'prescription_id' => $prescription->id,
                ...$item,
            ]);
        }

        return response()->json([
            'message' => __('medical.prescription_created'),
            'data' => new PrescriptionResource($prescription->load(['items', 'patient.user', 'doctor.user'])),
        ], 201);
    }
}
