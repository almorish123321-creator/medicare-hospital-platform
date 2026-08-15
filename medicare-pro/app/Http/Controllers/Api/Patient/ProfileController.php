<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\PatientResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        return response()->json([
            'data' => new PatientResource($patient->load('user')),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $patient = $request->user()->patient;
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'avatar' => 'sometimes|image|max:2048',
            'blood_type' => 'sometimes|in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'allergies' => 'sometimes|nullable|string',
            'chronic_diseases' => 'sometimes|nullable|string',
            'emergency_contact_name' => 'sometimes|nullable|string|max:255',
            'emergency_contact_phone' => 'sometimes|nullable|string|max:20',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|in:male,female,other',
            'address' => 'sometimes|nullable|string',
        ]);

        if (isset($validated['avatar'])) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        // Update user fields
        $userFields = ['name', 'phone', 'avatar'];
        $patientFields = ['blood_type', 'allergies', 'chronic_diseases', 'emergency_contact_name', 'emergency_contact_phone', 'date_of_birth', 'gender', 'address'];

        foreach ($userFields as $field) {
            if (isset($validated[$field])) {
                $user->$field = $validated[$field];
            }
        }
        $user->save();

        foreach ($patientFields as $field) {
            if (isset($validated[$field])) {
                $patient->$field = $validated[$field];
            }
        }
        $patient->save();

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.profile')]),
            'data' => new PatientResource($patient->fresh('user')),
        ]);
    }
}
