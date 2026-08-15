<?php

namespace App\Events;

use App\Models\Prescription;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrescriptionDispensed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Prescription $prescription) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('patient.' . $this->prescription->patient_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'prescription_id' => $this->prescription->id,
            'status' => 'dispensed',
        ];
    }

    public function broadcastAs(): string
    {
        return 'prescription.dispensed';
    }
}
