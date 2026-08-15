<?php

namespace App\Http\Controllers\Api\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\NotificationService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $invoices = Invoice::where('patient_id', $request->user()->patient->id)
            ->with(['hospital', 'appointment.doctor', 'payments'])
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => InvoiceResource::collection($invoices),
            'meta' => [
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'per_page' => $invoices->perPage(),
                'total' => $invoices->total(),
            ],
        ]);
    }

    public function pay(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->patient_id !== $request->user()->patient->id) {
            abort(403, __('messages.forbidden'));
        }

        if ($invoice->status === 'paid') {
            return response()->json(['message' => __('messages.no_changes')], 422);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $invoice->remaining_amount,
            'payment_method' => 'required|in:cash,card,online',
            'transaction_id' => 'nullable|string',
        ]);

        $payment = $this->paymentService->processPayment(
            $invoice,
            $validated['amount'],
            $validated['payment_method'],
            $validated['transaction_id'] ?? null
        );

        $this->notificationService->notifyPaymentSuccess($invoice->fresh());

        return response()->json([
            'message' => __('payments.payment_success'),
            'data' => new InvoiceResource($invoice->fresh('payments')),
        ]);
    }
}
