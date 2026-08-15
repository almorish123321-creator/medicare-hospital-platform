<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected PaymentService $paymentService
    ) {}

    /**
     * List patient's appointments
     * عرض مواعيد المريض
     *
     * @OA\Get(
     *     path="/v1/patient/appointments",
     *     tags={"Patient"},
     *     summary="List patient appointments | عرض مواعيد المريض",
     *     description="Returns a paginated list of the authenticated patient's appointments, ordered by most recent first. Includes doctor, hospital, and department details.
     * يُرجع قائمة paginated من مواعيد المريض المصادق عليه، مرتبة من الأحدث. يتضمن بيانات الطبيب والمستشفى والقسم.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page | عناصر في كل صفحة",
     *         @OA\Schema(type="integer", default=15, minimum=1, maximum=100)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of appointments | قائمة المواعيد",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/AppointmentResource")),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden - no hospital assigned | ممنوع - لم يتم تعيين مستشفى")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $appointments = $request->user()->patient->appointments()
            ->with(['doctor.department', 'hospital', 'department'])
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => AppointmentResource::collection($appointments),
            'meta' => [
                'current_page' => $appointments->currentPage(),
                'last_page' => $appointments->lastPage(),
                'per_page' => $appointments->perPage(),
                'total' => $appointments->total(),
            ],
        ]);
    }

    /**
     * Book a new appointment
     * حجز موعد جديد
     *
     * @OA\Post(
     *     path="/v1/patient/appointments",
     *     tags={"Patient"},
     *     summary="Book new appointment | حجز موعد جديد",
     *     description="Creates a new appointment for the authenticated patient. Sends notifications to both patient and doctor. Appointment date must be in the future.
     * ينشئ موعداً جديداً للمريض المصادق عليه. يُرسل إشعارات للمريض والطبيب.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"doctor_id","hospital_id","department_id","appointment_date","appointment_time"},
     *                 @OA\Property(property="doctor_id", type="integer", example=1, description="Doctor ID | معرف الطبيب"),
     *                 @OA\Property(property="hospital_id", type="integer", example=1, description="Hospital ID | معرف المستشفى"),
     *                 @OA\Property(property="department_id", type="integer", example=3, description="Department ID | معرف القسم"),
     *                 @OA\Property(property="appointment_date", type="string", format="date", example="2025-02-15", description="Appointment date (must be future) | تاريخ الموعد"),
     *                 @OA\Property(property="appointment_time", type="string", format="time", example="10:30", description="Appointment time | وقت الموعد"),
     *                 @OA\Property(property="symptoms", type="string", nullable=true, example="صداع وحمى", description="Symptoms description | وصف الأعراض"),
     *                 @OA\Property(property="notes", type="string", nullable=true, example="زيارة أولى", description="Additional notes | ملاحظات إضافية")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Appointment created | تم إنشاء الموعد",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error | خطأ في التحقق"),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden | ممنوع")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'hospital_id' => 'required|exists:hospitals,id',
            'department_id' => 'required|exists:departments,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required|date_format:H:i',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $appointment = Appointment::create([
            ...$validated,
            'patient_id' => $request->user()->patient->id,
            'status' => 'pending',
            'type' => 'booked',
        ]);

        $this->notificationService->notifyAppointmentBooked($appointment);

        return response()->json([
            'message' => __('appointments.appointment_created'),
            'data' => new AppointmentResource($appointment->load(['doctor.department', 'hospital', 'department', 'patient'])),
        ], 201);
    }

    /**
     * Get a single appointment details
     * عرض تفاصيل موعد محدد
     *
     * @OA\Get(
     *     path="/v1/patient/appointments/{appointment}",
     *     tags={"Patient"},
     *     summary="Get appointment details | عرض تفاصيل الموعد",
     *     description="Returns full details of a specific appointment belonging to the authenticated patient. Includes medical record if available.
     * يُرجع تفاصيل كاملة لموعد محدد يخص المريض المصادق عليه. يتضمن السجل الطبي إن وُجد.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *         description="Appointment ID | معرف الموعد",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment details | تفاصيل الموعد",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden - not your appointment | ممنوع - ليس موعدك"),
     *     @OA\Response(response=404, description="Appointment not found | الموعد غير موجود")
     * )
     */
    public function show(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorizeAppointment($appointment);
        return response()->json([
            'data' => new AppointmentResource($appointment->load(['doctor.department', 'hospital', 'department', 'patient', 'medicalRecord'])),
        ]);
    }

    /**
     * Cancel an appointment
     * إلغاء موعد
     *
     * @OA\Delete(
     *     path="/v1/patient/appointments/{appointment}",
     *     tags={"Patient"},
     *     summary="Cancel appointment | إلغاء الموعد",
     *     description="Cancels a pending or confirmed appointment. Sends notifications to both patient and doctor. Completed or in-progress appointments cannot be cancelled.
     * يُلغي موعداً معلقاً أو مؤكداً. يُرسل إشعارات للمريض والطبيب. لا يمكن إلغاء المواعيد المكتملة أو الجارية.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="appointment",
     *         in="path",
     *         required=true,
     *         description="Appointment ID | معرف الموعد",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Appointment cancelled | تم إلغاء الموعد",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden | ممنوع"),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot cancel this appointment status | لا يمكن إلغاء هذا الموعد",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $this->authorizeAppointment($appointment);

        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => __('messages.error')], 422);
        }

        $appointment->update(['status' => 'cancelled']);
        $this->notificationService->notifyAppointmentCancelled($appointment);

        return response()->json([
            'message' => __('appointments.appointment_cancelled'),
            'data' => new AppointmentResource($appointment),
        ]);
    }

    private function authorizeAppointment(Appointment $appointment): void
    {
        if ($appointment->patient_id !== request()->user()->patient->id) {
            abort(403, __('messages.forbidden'));
        }
    }
}
