<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\HospitalResource;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HospitalController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospitals = Hospital::withCount(['departments', 'doctors', 'users'])
            ->with(['subscriptionPlan'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => HospitalResource::collection($hospitals),
            'meta' => [
                'current_page' => $hospitals->currentPage(),
                'last_page' => $hospitals->lastPage(),
                'per_page' => $hospitals->perPage(),
                'total' => $hospitals->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:hospitals',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|string|email|max:255|unique:users',
            'admin_phone' => 'required|string|max:20|unique:users',
            'admin_password' => 'required|string|min:8',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'default_language' => 'nullable|in:ar,en',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($validated) {
            $hospital = Hospital::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'status' => 'active',
                'subscription_plan_id' => $validated['subscription_plan_id'] ?? null,
                'default_language' => $validated['default_language'] ?? 'ar',
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);

            $admin = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'phone' => $validated['admin_phone'],
                'password' => Hash::make($validated['admin_password']),
                'role' => 'hospital_admin',
                'hospital_id' => $hospital->id,
                'status' => 'active',
                'language_preference' => $validated['default_language'] ?? 'ar',
            ]);

            return response()->json([
                'message' => __('messages.created', ['model' => __('common.hospital')]),
                'data' => new HospitalResource($hospital->fresh('subscriptionPlan')),
            ], 201);
        });
    }

    public function update(Request $request, Hospital $hospital): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:hospitals,email,' . $hospital->id,
            'phone' => 'sometimes|string|max:50',
            'address' => 'sometimes|string',
            'status' => 'sometimes|in:active,inactive,suspended',
            'subscription_plan_id' => 'sometimes|nullable|exists:subscription_plans,id',
            'subscription_expires_at' => 'sometimes|nullable|date',
            'default_language' => 'sometimes|in:ar,en',
            'latitude' => 'sometimes|numeric',
            'longitude' => 'sometimes|numeric',
        ]);

        $hospital->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.hospital')]),
            'data' => new HospitalResource($hospital->fresh()),
        ]);
    }

    public function destroy(Request $request, Hospital $hospital): JsonResponse
    {
        $hospital->delete();
        return response()->json([
            'message' => __('messages.deleted', ['model' => __('common.hospital')]),
        ]);
    }
}
