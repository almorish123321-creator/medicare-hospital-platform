<?php

namespace App\Jobs;

use App\Events\PaymentProcessed;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ProcessPayment Job
 *
 * Processes an asynchronous payment for an invoice. Supports multiple
 * payment methods (cash, card, insurance, etc.) and updates the invoice
 * status accordingly (paid, partially_paid). After a successful payment
 * a PaymentProcessed event is fired so listeners can react (e.g. send
 * receipt, generate PDF).
 *
 * @property int    $payment_id     The ID of the payment record to process.
 * @property string $payment_method The method used (cash, card, insurance, etc.).
 * @property float  $amount         The payment amount.
 */
class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before each retry.
     */
    public int $backoff = 20;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * The supported payment methods.
     */
    private const SUPPORTED_METHODS = [
        'cash', 'credit_card', 'debit_card', 'insurance', 'bank_transfer', 'mobile_payment',
    ];

    /**
     * Create a new job instance.
     *
     * @param  int    $payment_id     The payment record ID.
     * @param  string $payment_method  The payment method slug.
     * @param  float  $amount          The amount to charge.
     */
    public function __construct(
        public int $payment_id,
        public string $payment_method,
        public float $amount,
    ) {
        $this->onQueue('payments');
    }

    /**
     * Execute the job.
     *
     * 1. Validates the payment method.
     * 2. Loads the payment and its invoice.
     * 3. Delegates to PaymentService for the actual processing.
     * 4. Fires a PaymentProcessed event on success.
     */
    public function handle(PaymentService $paymentService): void
    {
        if (! in_array($this->payment_method, self::SUPPORTED_METHODS, true)) {
            Log::warning('ProcessPayment: Unsupported payment method.', [
                'payment_id' => $this->payment_id,
                'payment_method' => $this->payment_method,
            ]);
            $this->fail("Unsupported payment method: {$this->payment_method}");
            return;
        }

        $payment = Payment::with('invoice')->find($this->payment_id);

        if (! $payment) {
            Log::warning("ProcessPayment: Payment #{$this->payment_id} not found.");
            $this->fail("Payment #{$this->payment_id} not found.");
            return;
        }

        if ($payment->status === 'success') {
            Log::info("ProcessPayment: Payment #{$this->payment_id} already processed. Skipping.");
            return;
        }

        try {
            DB::beginTransaction();

            // Update payment with the chosen method and amount
            $payment->update([
                'payment_method' => $this->payment_method,
                'amount' => $this->amount,
            ]);

            // Process through the service which handles invoice status transitions
            $processedPayment = $paymentService->processPayment(
                $payment->invoice,
                $this->amount,
                $this->payment_method,
                $this->generateTransactionId(),
            );

            DB::commit();

            // Fire event for downstream listeners (receipts, notifications, etc.)
            event(new PaymentProcessed($processedPayment));

            Log::info('ProcessPayment: Payment processed successfully.', [
                'payment_id' => $this->payment_id,
                'amount' => $this->amount,
                'method' => $this->payment_method,
                'invoice_status' => $payment->invoice->fresh()->status,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Mark payment as failed
            $payment->update(['status' => 'failed']);

            Log::error("ProcessPayment: Payment processing failed for #{$this->payment_id}: {$e->getMessage()}");

            throw $e; // Re-throw to trigger retry
        }
    }

    /**
     * Generate a unique transaction ID for the payment.
     */
    private function generateTransactionId(): string
    {
        return 'MCP-' . strtoupper(uniqid()) . '-' . now()->format('YmdHis');
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        // Ensure the payment record reflects the permanent failure
        Payment::where('id', $this->payment_id)->update(['status' => 'failed']);

        Log::error('ProcessPayment: Job failed permanently.', [
            'payment_id' => $this->payment_id,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'error' => $exception->getMessage(),
        ]);
    }
}
