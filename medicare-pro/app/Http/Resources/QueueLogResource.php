<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'queue_number' => $this->queue_number,
            'status' => $this->status,
            'estimated_wait_time' => $this->estimated_wait_time,
            'called_at' => $this->called_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}