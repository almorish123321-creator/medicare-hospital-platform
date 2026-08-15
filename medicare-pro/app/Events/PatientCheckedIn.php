<?php

namespace App\Events;

use App\Models\Appointment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatientCheckedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Appointment $appointment) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('hospital.' . $this->appointment->hospital_id),
            new Channel('queue.' . $this->appointment->department_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'queue_number' => $this->appointment->getQueueDisplayNumber(),
            'department_id' => $this->appointment->department_id,
            'patient_name' => $this->appointment->patient->user->name,
        ];
    }

    public function broadcastAs(): string
    {
        return 'patient.checked-in';
    }
}
