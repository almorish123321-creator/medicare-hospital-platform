<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'patient_id' => $this->patient_id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'appointment_id' => $this->appointment_id,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'doctor_id' => $this->doctor_id,
            'vital_signs' => $this->vital_signs,
            'symptoms' => $this->symptoms,
            'diagnosis' => $this->diagnosis,
            'notes' => $this->notes,
            'prescription' => new PrescriptionResource($this->whenLoaded('prescription')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
