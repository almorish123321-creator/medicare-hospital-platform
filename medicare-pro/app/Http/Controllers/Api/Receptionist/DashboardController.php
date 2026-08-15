<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;
        $today = now()->toDateString();

        $todayAppointments = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->count();

        $pendingAppointments = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'pending')
            ->count();

        $checkedIn = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'checked_in')
            ->count();

        $completedToday = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'data' => [
                'today_appointments' => $todayAppointments,
                'pending_appointments' => $pendingAppointments,
                'checked_in' => $checkedIn,
                'completed_today' => $completedToday,
            ],
        ]);
    }
}
