<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'patient_id' => $this->patient_id,
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'doctor_id' => $this->doctor_id,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'hospital_id' => $this->hospital_id,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'department_id' => $this->department_id,
            'appointment_date' => $this->appointment_date->toIso8601String(),
            'appointment_time' => $this->appointment_time,
            'queue_number' => $this->queue_number,
            'queue_display_number' => $this->getQueueDisplayNumber(),
            'status' => $this->status,
            'type' => $this->type,
            'symptoms' => $this->symptoms,
            'notes' => $this->notes,
            'checked_in_at' => $this->checked_in_at?->toIso8601String(),
            'started_at' => $this->started_at?->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'medical_record' => new MedicalRecordResource($this->whenLoaded('medicalRecord')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}