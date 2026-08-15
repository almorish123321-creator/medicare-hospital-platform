<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrescriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medical_record' => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'medical_record_id' => $this->medical_record_id,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'doctor_id' => $this->doctor_id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'patient_id' => $this->patient_id,
            'diagnosis' => $this->diagnosis,
            'instructions' => $this->instructions,
            'status' => $this->status,
            'items' => PrescriptionItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}