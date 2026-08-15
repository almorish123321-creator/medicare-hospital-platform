<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'user_id' => $this->user_id,
            'department_id' => $this->department_id,
            'department' => new DepartmentResource($this->whenLoaded('department')),
            'specialty' => $this->specialty,
            'qualification' => $this->qualification,
            'experience_years' => $this->experience_years,
            'consultation_fee' => (float) $this->consultation_fee,
            'rating' => (float) $this->rating,
            'total_reviews' => $this->total_reviews,
            'bio' => $this->bio,
            'is_available' => $this->is_available,
            'schedule_settings' => $this->schedule_settings,
            'hospital' => new HospitalResource($this->when($this->relationLoaded('department') && $this->department && $this->department->relationLoaded('hospital'), fn() => $this->department->hospital)),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}