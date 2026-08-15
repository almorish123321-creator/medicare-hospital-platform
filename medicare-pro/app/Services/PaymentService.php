<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function processPayment(Invoice $invoice, float $amount, string $method, ?string $transactionId = null): Payment
    {
        return DB::transaction(function () use ($invoice, $amount, $method, $transactionId) {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'payment_method' => $method,
                'transaction_id' => $transactionId,
                'status' => 'success',
                'paid_at' => now(),
            ]);

            $paidAmount = $invoice->payments()->where('status', 'success')->sum('amount');

            if ($paidAmount >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid', 'paid_at' => now()]);
            } elseif ($paidAmount > 0) {
                $invoice->update(['status' => 'partially_paid']);
            }

            return $payment;
        });
    }

    public function generateInvoice(array $data): Invoice
    {
        $taxRate = 0.15; // 15% tax
        $amount = $data['amount'] ?? 0;
        $discount = $data['discount'] ?? 0;
        $tax = ($amount - $discount) * $taxRate;
        $totalAmount = ($amount - $discount) + $tax;

        return Invoice::create([
            'patient_id' => $data['patient_id'],
            'appointment_id' => $data['appointment_id'] ?? null,
            'hospital_id' => $data['hospital_id'],
            'amount' => $amount,
            'discount' => $discount,
            'tax' => round($tax, 2),
            'total_amount' => round($totalAmount, 2),
            'status' => 'pending',
        ]);
    }

    public function processRefund(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->update(['status' => 'refunded']);
            return $invoice;
        });
    }
}
