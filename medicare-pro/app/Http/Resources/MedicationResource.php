<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'hospital_id' => $this->hospital_id,
            'name' => $this->name,
            'generic_name' => $this->generic_name,
            'category' => $this->category,
            'stock_quantity' => $this->stock_quantity,
            'unit' => $this->unit,
            'price' => (float) $this->price,
            'expiry_date' => $this->expiry_date?->toIso8601String(),
            'status' => $this->status,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}