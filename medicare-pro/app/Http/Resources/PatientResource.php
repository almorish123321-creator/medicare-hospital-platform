<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'user_id' => $this->user_id,
            'blood_type' => $this->blood_type,
            'allergies' => $this->allergies,
            'chronic_diseases' => $this->chronic_diseases,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'date_of_birth' => $this->date_of_birth?->toIso8601String(),
            'gender' => $this->gender,
            'address' => $this->address,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}