<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $records = MedicalRecord::where('patient_id', $request->user()->patient->id)
            ->with(['doctor.user', 'appointment'])
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => MedicalRecordResource::collection($records),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'per_page' => $records->perPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    public function show(Request $request, MedicalRecord $medicalRecord): JsonResponse
    {
        if ($medicalRecord->patient_id !== $request->user()->patient->id) {
            abort(403, __('messages.forbidden'));
        }

        return response()->json([
            'data' => new MedicalRecordResource($medicalRecord->load(['doctor.user', 'appointment', 'prescription.items'])),
        ]);
    }
}
