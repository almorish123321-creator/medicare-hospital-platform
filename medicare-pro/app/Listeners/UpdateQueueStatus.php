<?php

namespace App\Listeners;

use App\Events\PatientCheckedIn;
use App\Events\QueueUpdated;
use App\Services\QueueService;

class UpdateQueueStatus
{
    public function __construct(protected QueueService $queueService) {}

    public function handle(PatientCheckedIn $event): void
    {
        $departmentId = $event->appointment->department_id;
        $queueData = $this->queueService->getQueueStatus($departmentId);
        event(new QueueUpdated($departmentId, $queueData));
    }
}
