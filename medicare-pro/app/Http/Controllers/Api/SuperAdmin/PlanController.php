<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPlanResource;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $plans = SubscriptionPlan::latest()->paginate($request->per_page ?? 20);
        return response()->json([
            'data' => SubscriptionPlanResource::collection($plans),
            'meta' => [
                'current_page' => $plans->currentPage(),
                'last_page' => $plans->lastPage(),
                'per_page' => $plans->perPage(),
                'total' => $plans->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'max_doctors' => 'required|integer|min:1',
            'max_departments' => 'required|integer|min:1',
            'max_patients_per_month' => 'required|integer|min:1',
            'features' => 'nullable|array',
        ]);

        $plan = SubscriptionPlan::create([
            ...$validated,
            'status' => 'active',
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => 'Plan']),
            'data' => new SubscriptionPlanResource($plan),
        ], 201);
    }

    public function update(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'duration_days' => 'sometimes|integer|min:1',
            'max_doctors' => 'sometimes|integer|min:1',
            'max_departments' => 'sometimes|integer|min:1',
            'max_patients_per_month' => 'sometimes|integer|min:1',
            'features' => 'sometimes|nullable|array',
            'status' => 'sometimes|in:active,inactive',
        ]);

        $plan->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => 'Plan']),
            'data' => new SubscriptionPlanResource($plan->fresh()),
        ]);
    }

    public function destroy(Request $request, SubscriptionPlan $plan): JsonResponse
    {
        $plan->delete();
        return response()->json([
            'message' => __('messages.deleted', ['model' => 'Plan']),
        ]);
    }
}
