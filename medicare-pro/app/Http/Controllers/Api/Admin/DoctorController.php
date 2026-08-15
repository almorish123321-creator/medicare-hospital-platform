<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;
        $doctors = Doctor::whereHas('department', fn($q) => $q->where('hospital_id', $hospitalId))
            ->with(['user', 'department'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => DoctorResource::collection($doctors),
            'meta' => [
                'current_page' => $doctors->currentPage(),
                'last_page' => $doctors->lastPage(),
                'per_page' => $doctors->perPage(),
                'total' => $doctors->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'password' => 'required|string|min:8',
            'department_id' => 'required|exists:departments,id,hospital_id,' . $request->user()->hospital_id,
            'specialty' => 'required|string|max:255',
            'qualification' => 'required|string',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'nullable|numeric|min:0',
            'bio' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'doctor',
            'hospital_id' => $request->user()->hospital_id,
            'status' => 'active',
            'language_preference' => 'ar',
        ]);

        $doctor = Doctor::create([
            'user_id' => $user->id,
            'department_id' => $validated['department_id'],
            'specialty' => $validated['specialty'],
            'qualification' => $validated['qualification'],
            'experience_years' => $validated['experience_years'] ?? 0,
            'consultation_fee' => $validated['consultation_fee'] ?? 0,
            'bio' => $validated['bio'] ?? null,
            'is_available' => true,
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => __('common.doctor')]),
            'data' => new DoctorResource($doctor->load('user', 'department')),
        ], 201);
    }

    public function update(Request $request, Doctor $doctor): JsonResponse
    {
        $this->authorizeDoctor($doctor, $request);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $doctor->user_id,
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $doctor->user_id,
            'department_id' => 'sometimes|exists:departments,id',
            'specialty' => 'sometimes|string|max:255',
            'qualification' => 'sometimes|string',
            'experience_years' => 'sometimes|integer|min:0',
            'consultation_fee' => 'sometimes|numeric|min:0',
            'bio' => 'sometimes|nullable|string',
            'is_available' => 'sometimes|boolean',
        ]);

        if (isset($validated['name']) || isset($validated['email']) || isset($validated['phone'])) {
            $doctor->user->update(
                array_intersect_key($validated, array_flip(['name', 'email', 'phone']))
            );
        }

        $doctorFields = ['department_id', 'specialty', 'qualification', 'experience_years', 'consultation_fee', 'bio', 'is_available'];
        $doctor->update(array_intersect_key($validated, array_flip($doctorFields)));

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.doctor')]),
            'data' => new DoctorResource($doctor->fresh('user', 'department')),
        ]);
    }

    public function destroy(Request $request, Doctor $doctor): JsonResponse
    {
        $this->authorizeDoctor($doctor, $request);
        $doctor->delete();
        $doctor->user->delete();

        return response()->json([
            'message' => __('messages.deleted', ['model' => __('common.doctor')]),
        ]);
    }

    private function authorizeDoctor(Doctor $doctor, Request $request): void
    {
        if ($doctor->department->hospital_id !== $request->user()->hospital_id) {
            abort(403, __('messages.forbidden'));
        }
    }
}
