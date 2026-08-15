<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'period' => 'sometimes|in:daily,weekly,monthly',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date',
        ]);

        $hospitalId = $request->user()->hospital_id;
        $period = $request->get('period', 'daily');

        $query = Appointment::where('hospital_id', $hospitalId);

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $appointmentStats = [
            'total' => (clone $query)->count(),
            'completed' => (clone $query)->where('status', 'completed')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'no_show' => (clone $query)->where('status', 'no_show')->count(),
        ];

        $revenueQuery = Invoice::where('hospital_id', $hospitalId)
            ->where('status', 'paid');
        if ($request->filled('from')) {
            $revenueQuery->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $revenueQuery->whereDate('created_at', '<=', $request->to);
        }

        $revenueStats = [
            'total_revenue' => (float) (clone $revenueQuery)->sum('total_amount'),
            'total_appointments' => $appointmentStats['total'],
            'average_per_appointment' => $appointmentStats['total'] > 0
                ? (float) ((clone $revenueQuery)->sum('total_amount') / $appointmentStats['total'])
                : 0,
        ];

        return response()->json([
            'data' => [
                'appointments' => $appointmentStats,
                'revenue' => $revenueStats,
            ],
        ]);
    }

    public function doctorPerformance(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;

        $doctors = Doctor::whereHas('department', fn($q) => $q->where('hospital_id', $hospitalId))
            ->withCount(['appointments', 'appointments as completed_appointments' => fn($q) => $q->where('status', 'completed')])
            ->withSum(['appointments' => function($q) {
                $q->where('status', 'completed')
                    ->whereHas('invoice', fn($iq) => $iq->where('status', 'paid'));
            }], 'consultation_fee')
            ->get();

        $doctorStats = $doctors->map(fn($d) => [
            'id' => $d->id,
            'name' => $d->user->name,
            'specialty' => $d->specialty,
            'total_appointments' => $d->appointments_count,
            'completed_appointments' => $d->completed_appointments,
            'rating' => (float) $d->rating,
            'revenue_generated' => (float) ($d->appointments_sum_consultation_fee ?? 0),
        ]);

        return response()->json(['data' => $doctorStats]);
    }

    public function patientDemographics(Request $request): JsonResponse
    {
        $hospitalId = $request->user()->hospital_id;

        $genderStats = Patient::selectRaw('gender, COUNT(*) as count')
            ->whereHas('user', fn($q) => $q->where('hospital_id', $hospitalId))
            ->groupBy('gender')
            ->get();

        $ageGroups = Patient::selectRaw("
            CASE
                WHEN date_of_birth >= DATE_SUB(CURDATE(), INTERVAL 18 YEAR) THEN '0-17'
                WHEN date_of_birth >= DATE_SUB(CURDATE(), INTERVAL 30 YEAR) THEN '18-29'
                WHEN date_of_birth >= DATE_SUB(CURDATE(), INTERVAL 45 YEAR) THEN '30-44'
                WHEN date_of_birth >= DATE_SUB(CURDATE(), INTERVAL 60 YEAR) THEN '45-59'
                ELSE '60+'
            END as age_group, COUNT(*) as count
        ")
            ->whereHas('user', fn($q) => $q->where('hospital_id', $hospitalId))
            ->groupBy('age_group')
            ->get();

        return response()->json([
            'data' => [
                'by_gender' => $genderStats,
                'by_age_group' => $ageGroups,
            ],
        ]);
    }
}
