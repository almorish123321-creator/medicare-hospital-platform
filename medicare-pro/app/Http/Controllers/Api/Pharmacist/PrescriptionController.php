<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrescriptionResource;
use App\Models\Medication;
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
        $prescriptions = Prescription::where('status', 'pending')
            ->whereHas('medicalRecord.appointment', function ($q) use ($request) {
                $q->where('hospital_id', $request->user()->hospital_id);
            })
            ->with(['patient.user', 'doctor.user', 'items', 'medicalRecord.appointment'])
            ->latest()
            ->paginate($request->per_page ?? 20);

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
        if ($prescription->medicalRecord->appointment->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        return response()->json([
            'data' => new PrescriptionResource($prescription->load(['items', 'patient.user', 'doctor.user', 'medicalRecord'])),
        ]);
    }

    public function dispense(Request $request, Prescription $prescription): JsonResponse
    {
        if ($prescription->status !== 'pending') {
            return response()->json(['message' => __('messages.error')], 422);
        }

        // Check medication availability and update stock
        foreach ($prescription->items as $item) {
            $medication = Medication::where('hospital_id', $request->user()->hospital_id)
                ->where('name', 'like', "%{$item->medication_name}%")
                ->first();

            if ($medication && $medication->stock_quantity > 0) {
                $medication->decrement('stock_quantity');
                $medication->updateStatus();
            }
        }

        $prescription->update(['status' => 'dispensed']);
        $this->notificationService->notifyPrescriptionReady($prescription);

        return response()->json([
            'message' => __('messages.prescription_dispensed'),
            'data' => new PrescriptionResource($prescription->fresh()),
        ]);
    }
}
