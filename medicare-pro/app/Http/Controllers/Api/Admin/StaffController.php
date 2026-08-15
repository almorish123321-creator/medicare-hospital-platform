<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function receptionists(Request $request): JsonResponse
    {
        $users = User::where('hospital_id', $request->user()->hospital_id)
            ->where('role', 'receptionist')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function addReceptionist(Request $request): JsonResponse
    {
        return $this->addStaff($request, 'receptionist');
    }

    public function nurses(Request $request): JsonResponse
    {
        $users = User::where('hospital_id', $request->user()->hospital_id)
            ->where('role', 'nurse')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function addNurse(Request $request): JsonResponse
    {
        return $this->addStaff($request, 'nurse');
    }

    public function pharmacists(Request $request): JsonResponse
    {
        $users = User::where('hospital_id', $request->user()->hospital_id)
            ->where('role', 'pharmacist')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function addPharmacist(Request $request): JsonResponse
    {
        return $this->addStaff($request, 'pharmacist');
    }

    private function addStaff(Request $request, string $role): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
            'hospital_id' => $request->user()->hospital_id,
            'status' => 'active',
            'language_preference' => Hospital::find($request->user()->hospital_id)?->default_language ?? 'ar',
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => __("common.{$role}")]),
            'data' => new UserResource($user),
        ], 201);
    }
}
