<?php

namespace App\Http\Controllers\Api\Pharmacist;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicationResource;
use App\Models\Medication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $medications = Medication::where('hospital_id', $request->user()->hospital_id)
            ->withCount(['prescriptionItems as total_prescribed'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => MedicationResource::collection($medications),
            'meta' => [
                'current_page' => $medications->currentPage(),
                'last_page' => $medications->lastPage(),
                'per_page' => $medications->perPage(),
                'total' => $medications->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock_quantity' => 'required|integer|min:0',
            'unit' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'expiry_date' => 'required|date|after:today',
        ]);

        $medication = Medication::create([
            ...$validated,
            'hospital_id' => $request->user()->hospital_id,
            'status' => $validated['stock_quantity'] <= 10 ? 'low_stock' : 'available',
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => __('medical.medication')]),
            'data' => new MedicationResource($medication),
        ], 201);
    }

    public function update(Request $request, Medication $medication): JsonResponse
    {
        if ($medication->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }

        $validated = $request->validate([
            'stock_quantity' => 'sometimes|integer|min:0',
            'price' => 'sometimes|numeric|min:0',
            'expiry_date' => 'sometimes|date',
            'name' => 'sometimes|string|max:255',
            'generic_name' => 'sometimes|string|max:255',
            'category' => 'sometimes|string|max:100',
            'unit' => 'sometimes|string|max:50',
        ]);

        $medication->update($validated);
        $medication->updateStatus();

        return response()->json([
            'message' => __('messages.updated', ['model' => __('medical.medication')]),
            'data' => new MedicationResource($medication->fresh()),
        ]);
    }

    public function inventory(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;

        $available = Medication::where('hospital_id', $hospitalId)->where('status', 'available')->count();
        $lowStock = Medication::where('hospital_id', $hospitalId)->where('status', 'low_stock')->count();
        $outOfStock = Medication::where('hospital_id', $hospitalId)->where('status', 'out_of_stock')->count();
        $expired = Medication::where('hospital_id', $hospitalId)->where('status', 'expired')->count();
        $expiringSoon = Medication::where('hospital_id', $hospitalId)->expiringSoon(30)->count();

        $categories = Medication::where('hospital_id', $hospitalId)
            ->selectRaw('category, COUNT(*) as count, SUM(stock_quantity) as total_stock')
            ->groupBy('category')
            ->get();

        return response()->json([
            'data' => [
                'available' => $available,
                'low_stock' => $lowStock,
                'out_of_stock' => $outOfStock,
                'expired' => $expired,
                'expiring_soon' => $expiringSoon,
                'by_category' => $categories,
            ],
        ]);
    }
}
