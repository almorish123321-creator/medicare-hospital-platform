<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Models\Hospital;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Patient::whereHas('user', fn($q) => $q->where('hospital_id', $request->user()->hospital_id))
            ->with('user');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $patients = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => PatientResource::collection($patients),
            'meta' => [
                'current_page' => $patients->currentPage(),
                'last_page' => $patients->lastPage(),
                'per_page' => $patients->perPage(),
                'total' => $patients->total(),
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
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'blood_type' => 'nullable|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'address' => 'nullable|string',
            'allergies' => 'nullable|string',
            'chronic_diseases' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'role' => 'patient',
            'hospital_id' => $request->user()->hospital_id,
            'status' => 'active',
            'language_preference' => Hospital::find($request->user()->hospital_id)->default_language ?? 'ar',
        ]);

        $patient = Patient::create([
            'user_id' => $user->id,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'blood_type' => $validated['blood_type'] ?? null,
            'address' => $validated['address'] ?? null,
            'allergies' => $validated['allergies'] ?? null,
            'chronic_diseases' => $validated['chronic_diseases'] ?? null,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
        ]);

        return response()->json([
            'message' => __('messages.created', ['model' => __('common.patient')]),
            'data' => new PatientResource($patient->load('user')),
        ], 201);
    }
}
