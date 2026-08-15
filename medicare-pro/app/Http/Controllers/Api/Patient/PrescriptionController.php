<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionResource;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $prescriptions = Prescription::where('patient_id', $request->user()->patient->id)
            ->with(['doctor.user', 'medicalRecord.appointment', 'items'])
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

    public function show(Request $request, Prescription $prescription): JsonResponse
    {
        if ($prescription->patient_id !== $request->user()->patient->id) {
            abort(403, __('messages.forbidden'));
        }

        return response()->json([
            'data' => new PrescriptionResource($prescription->load(['doctor.user', 'items'])),
        ]);
    }
}
