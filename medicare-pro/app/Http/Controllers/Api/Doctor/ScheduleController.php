<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;
        return response()->json([
            'data' => [
                'schedule_settings' => $doctor->schedule_settings ?? [],
                'is_available' => $doctor->is_available,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;

        $validated = $request->validate([
            'schedule_settings' => 'nullable|array',
            'schedule_settings.*.day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
            'schedule_settings.*.start_time' => 'required|date_format:H:i',
            'schedule_settings.*.end_time' => 'required|date_format:H:i',
            'schedule_settings.*.is_working' => 'required|boolean',
            'is_available' => 'nullable|boolean',
        ]);

        $doctor->update($validated);

        return response()->json([
            'message' => __('messages.updated', ['model' => __('common.settings')]),
            'data' => [
                'schedule_settings' => $doctor->fresh()->schedule_settings,
                'is_available' => $doctor->fresh()->is_available,
            ],
        ]);
    }

    public function patients(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;
        $patients = $doctor->appointments()
            ->with('patient.user')
            ->distinct('patient_id')
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => \App\Http\Resources\PatientResource::collection(
                $patients->pluck('patient')->unique()
            ),
        ]);
    }
}
