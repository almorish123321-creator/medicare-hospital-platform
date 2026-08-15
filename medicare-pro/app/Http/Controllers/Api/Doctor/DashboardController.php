<?php

namespace App\Http\Controllers\Api\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $doctor = $request->user()->doctor;
        $today = now()->toDateString();

        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->count();

        $pendingAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->count();

        $completedAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->count();

        $totalPatients = Appointment::where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        $rating = $doctor->rating;

        return response()->json([
            'data' => [
                'today_appointments' => $todayAppointments,
                'pending_appointments' => $pendingAppointments,
                'completed_appointments' => $completedAppointments,
                'total_patients' => $totalPatients,
                'rating' => (float) $rating,
                'total_reviews' => $doctor->total_reviews,
            ],
        ]);
    }
}
