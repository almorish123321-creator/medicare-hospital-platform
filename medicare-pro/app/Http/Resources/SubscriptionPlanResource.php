<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'duration_days' => $this->duration_days,
            'max_doctors' => $this->max_doctors,
            'max_departments' => $this->max_departments,
            'max_patients_per_month' => $this->max_patients_per_month,
            'features' => $this->features,
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}