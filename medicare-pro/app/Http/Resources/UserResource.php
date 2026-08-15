<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset("storage/{$this->avatar}") : null,
            'role' => $this->role,
            'status' => $this->status,
            'language_preference' => $this->language_preference,
            'email_verified_at' => $this->email_verified_at,
            'hospital' => new HospitalResource($this->whenLoaded('hospital')),
            'doctor' => new DoctorResource($this->whenLoaded('doctor')),
            'patient' => new PatientResource($this->whenLoaded('patient')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}