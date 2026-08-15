<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Hospital;
use App\Models\Invoice;
use App\Models\SubscriptionPlan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $totalHospitals = Hospital::count();
        $activeHospitals = Hospital::active()->count();
        $inactiveHospitals = Hospital::where('status', 'inactive')->count();
        $suspendedHospitals = Hospital::where('status', 'suspended')->count();

        $totalDoctors = \App\Models\Doctor::count();
        $totalPatients = \App\Models\Patient::count();
        $totalAppointments = Appointment::count();

        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $subscriptionRevenue = SubscriptionPlan::withCount('hospitals')->get();

        return response()->json([
            'data' => [
                'hospitals' => [
                    'total' => $totalHospitals,
                    'active' => $activeHospitals,
                    'inactive' => $inactiveHospitals,
                    'suspended' => $suspendedHospitals,
                ],
                'users' => [
                    'total_doctors' => $totalDoctors,
                    'total_patients' => $totalPatients,
                ],
                'appointments' => [
                    'total' => $totalAppointments,
                ],
                'revenue' => [
                    'total' => (float) $totalRevenue,
                    'monthly' => (float) $monthlyRevenue,
                ],
                'subscriptions' => $subscriptionRevenue,
            ],
        ]);
    }
}
