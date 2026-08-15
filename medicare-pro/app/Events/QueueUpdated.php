<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $departmentId,
        public array $queueData
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('hospital.department.' . $this->departmentId),
            new Channel('queue.' . $this->departmentId),
        ];
    }

    public function broadcastWith(): array
    {
        return $this->queueData;
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }
}
