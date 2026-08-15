<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\HospitalResource;
use App\Http\Resources\ReviewResource;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function hospitals(Request $request): JsonResponse
    {
        $hospitals = Hospital::active()
            ->withCount('departments')
            ->when($request->filled('search'), fn($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'data' => HospitalResource::collection($hospitals),
            'meta' => [
                'current_page' => $hospitals->currentPage(),
                'last_page' => $hospitals->lastPage(),
                'per_page' => $hospitals->perPage(),
                'total' => $hospitals->total(),
            ],
        ]);
    }

    public function hospital(Hospital $hospital): JsonResponse
    {
        return response()->json([
            'data' => new HospitalResource($hospital->load(['departments', 'departments.doctors'])),
        ]);
    }

    public function hospitalDoctors(Request $request, Hospital $hospital): JsonResponse
    {
        $doctors = Doctor::whereHas('department', fn($q) => $q->where('hospital_id', $hospital->id))
            ->available()
            ->with(['user', 'department'])
            ->when($request->filled('department_id'), fn($q) => $q->where('department_id', $request->department_id))
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

    public function hospitalDepartments(Request $request, Hospital $hospital): JsonResponse
    {
        $departments = $hospital->departments()->active()->withCount('doctors')->get();
        return response()->json(['data' => DepartmentResource::collection($departments)]);
    }

    public function doctor(Doctor $doctor): JsonResponse
    {
        return response()->json([
            'data' => new DoctorResource($doctor->load(['user', 'department', 'department.hospital'])),
        ]);
    }

    public function doctorReviews(Request $request, Doctor $doctor): JsonResponse
    {
        $reviews = $doctor->reviews()->approved()->latest()->paginate($request->per_page ?? 20);
        return response()->json([
            'data' => ReviewResource::collection($reviews),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function doctorSchedule(Request $request, Doctor $doctor): JsonResponse
    {
        $schedule = $doctor->schedule_settings ?? [];
        $availableSlots = $this->getAvailableSlots($doctor);

        return response()->json([
            'data' => [
                'schedule' => $schedule,
                'available_slots' => $availableSlots,
                'is_available' => $doctor->is_available,
            ],
        ]);
    }

    public function languages(): JsonResponse
    {
        $locales = config('app.available_locales', ['ar' => 'Arabic', 'en' => 'English']);
        return response()->json(['data' => $locales]);
    }

    private function getAvailableSlots(Doctor $doctor): array
    {
        $schedule = $doctor->schedule_settings ?? [];
        $date = now()->addDays(7); // Next 7 days
        $slots = [];

        for ($i = 1; $i <= 7; $i++) {
            $day = now()->addDays($i);
            $dayName = strtolower($day->englishDayOfWeek());

            $daySchedule = collect($schedule)->first(fn($s) => strtolower($s['day']) === $dayName);

            if ($daySchedule && ($daySchedule['is_working'] ?? false)) {
                $bookedTimes = Appointment::where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $day->toDateString())
                    ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
                    ->pluck('appointment_time')
                    ->toArray();

                $slots[$day->toDateString()] = [
                    'date' => $day->toDateString(),
                    'day' => $dayName,
                    'start_time' => $daySchedule['start_time'],
                    'end_time' => $daySchedule['end_time'],
                    'booked_times' => $bookedTimes,
                ];
            }
        }

        return $slots;
    }
}
