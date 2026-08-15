<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;
        $today = now()->toDateString();

        $todayAppointments = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)->count();

        $totalDoctors = Doctor::whereHas('department', fn($q) => $q->where('hospital_id', $hospitalId))->count();

        $totalPatients = Patient::whereHas('user', fn($q) => $q->where('hospital_id', $hospitalId))->count();

        $totalRevenue = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'paid')->sum('total_amount');

        $monthlyRevenue = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $noShowRate = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'no_show')
            ->count();

        $completedToday = Appointment::where('hospital_id', $hospitalId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->count();

        return response()->json([
            'data' => [
                'today_appointments' => $todayAppointments,
                'total_doctors' => $totalDoctors,
                'total_patients' => $totalPatients,
                'total_revenue' => (float) $totalRevenue,
                'monthly_revenue' => (float) $monthlyRevenue,
                'no_show_rate' => $noShowRate,
                'completed_today' => $completedToday,
            ],
        ]);
    }
}
