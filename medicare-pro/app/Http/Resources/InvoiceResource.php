<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'patient_id' => $this->patient_id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'appointment_id' => $this->appointment_id,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'hospital_id' => $this->hospital_id,
            'amount' => (float) $this->amount,
            'discount' => (float) $this->discount,
            'tax' => (float) $this->tax,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'insurance_provider' => $this->insurance_provider,
            'insurance_coverage' => $this->insurance_coverage ? (float) $this->insurance_coverage : null,
            'paid_amount' => (float) $this->getPaidAmountAttribute(),
            'remaining_amount' => (float) $this->getRemainingAmountAttribute(),
            'paid_at' => $this->paid_at?->toIso8601String(),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}