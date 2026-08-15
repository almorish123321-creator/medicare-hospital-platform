<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentResource;
use App\Services\NotificationService;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(
        protected QueueService $queueService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Get queue status for the receptionist
     * عرض حالة الطابور للموظف
     *
     * @OA\Get(
     *     path="/v1/receptionist/queue",
     *     tags={"Receptionist"},
     *     summary="Get queue status | عرض حالة الطابور",
     *     description="Returns the current queue status. If department_id is provided, returns detailed queue info for that department (current serving, next patients, wait times). Otherwise, returns all waiting patients across the hospital.
     * يُرجع حالة الطابور الحالية. إذا تم توفير department_id، يُرجع معلومات تفصيلية عن طابور القسم. وإلا يُرجع جميع المرضى المنتظرين عبر المستشفى.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="department_id",
     *         in="query",
     *         description="Filter by department ID | تصفية حسب معرف القسم",
     *         required=false,
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Queue status data | بيانات حالة الطابور",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="current_serving", type="string", nullable=true, example="D3-001", description="Currently serving queue number | رقم الطابور الحالي"),
     *                         @OA\Property(property="next_patients", type="array", @OA\Items(type="object",
     *                             @OA\Property(property="queue_number", type="string", example="D3-002"),
     *                             @OA\Property(property="patient", type="string", example="أحمد محمد")
     *                         )),
     *                         @OA\Property(property="completed_today", type="integer", example=15, description="Completed appointments today | المواعيد المكتملة اليوم"),
     *                         @OA\Property(property="average_wait_time", type="integer", example=30, description="Average wait in minutes | متوسط الانتظار بالدقائق"),
     *                         @OA\Property(property="waiting_count", type="integer", example=8, description="Number of waiting patients | عدد المرضى المنتظرين")
     *                     )
     *                 ),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="data", type="object",
     *                         @OA\Property(property="waiting_patients", type="array", @OA\Items(ref="#/components/schemas/AppointmentResource")),
     *                         @OA\Property(property="total_waiting", type="integer", example=12)
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden | ممنوع")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $departmentId = $request->query('department_id');
        $hospitalId = $request->user()->hospital_id;

        if ($departmentId) {
            $status = $this->queueService->getQueueStatus($departmentId);
        } else {
            $waiting = \App\Models\Appointment::where('hospital_id', $hospitalId)
                ->whereDate('appointment_date', today())
                ->whereIn('status', ['checked_in', 'pending'])
                ->whereNotNull('queue_number')
                ->with(['patient.user', 'doctor.user', 'department', 'queueLog'])
                ->orderBy('queue_number')
                ->get();

            $status = [
                'waiting_patients' => AppointmentResource::collection($waiting),
                'total_waiting' => $waiting->count(),
            ];
        }

        return response()->json(['data' => $status]);
    }

    /**
     * Call the next patient in queue
     * استدعاء المريض التالي في الطابور
     *
     * @OA\Post(
     *     path="/v1/receptionist/queue/{id}/call",
     *     tags={"Receptionist"},
     *     summary="Call next patient | استدعاء المريض التالي",
     *     description="Calls the next checked-in patient in the queue for a given department. Updates appointment status to in_progress and sends notification to the patient. Returns null if no patients are waiting.
     * يستدعي المريض المنتظر التالي في طابور القسم المحدد. يُحدّث حالة الموعد إلى جاري ويُرسل إشعاراً للمريض.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID | معرف القسم",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"department_id"},
     *                 @OA\Property(property="department_id", type="integer", example=3, description="Department ID | معرف القسم")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Next patient called or no patients waiting | تم استدعاء المريض التالي أو لا يوجد مرضى",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Patient called"),
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden | ممنوع"),
     *     @OA\Response(response=422, description="Validation error | خطأ في التحقق")
     * )
     */
    public function callNext(Request $request): JsonResponse
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);

        $next = $this->queueService->callNext($request->department_id);

        if ($next) {
            $this->notificationService->notifyQueueCalled($next);
        }

        return response()->json([
            'message' => $next ? __('messages.queue_called') : __('appointments.no_appointments'),
            'data' => $next ? new AppointmentResource($next->fresh('queueLog')) : null,
        ]);
    }

    /**
     * Skip the current patient in queue
     * تخطي المريض الحالي في الطابور
     *
     * @OA\Post(
     *     path="/v1/receptionist/queue/{id}/skip",
     *     tags={"Receptionist"},
     *     summary="Skip current patient | تخطي المريض الحالي",
     *     description="Skips the currently in-progress patient and returns them to the checked_in queue. The patient will be called again later in their original order.
     * يتخطى المريض الحالي الجاري ويُعيده لطابور المنتظرين. سيتم استدعاء المريض مرة أخرى لاحقاً.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Department ID | معرف القسم",
     *         @OA\Schema(type="integer", example=3)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"department_id"},
     *                 @OA\Property(property="department_id", type="integer", example=3, description="Department ID | معرف القسم")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Patient skipped or no appointment in progress | تم تخطي المريض أو لا يوجد موعد جارٍ",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/AppointmentResource", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated | غير مصادق"),
     *     @OA\Response(response=403, description="Forbidden | ممنوع"),
     *     @OA\Response(response=422, description="Validation error | خطأ في التحقق")
     * )
     */
    public function skipCurrent(Request $request): JsonResponse
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);

        $skipped = $this->queueService->skipCurrent($request->department_id);

        return response()->json([
            'message' => $skipped ? __('messages.success') : __('appointments.no_appointments'),
            'data' => $skipped ? new AppointmentResource($skipped->fresh()) : null,
        ]);
    }
}
