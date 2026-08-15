<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hospital_id' => $this->hospital_id,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'status' => $this->status,
            'doctors_count' => $this->whenCounted('doctors'),
            'doctors' => DoctorResource::collection($this->whenLoaded('doctors')),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}