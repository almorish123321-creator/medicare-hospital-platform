<?php

namespace App\Listeners;

use App\Events\AppointmentCreated;
use App\Services\NotificationService;

class SendAppointmentNotification
{
    public function __construct(protected NotificationService $notificationService) {}

    public function handle(AppointmentCreated $event): void
    {
        $this->notificationService->notifyAppointmentBooked($event->appointment);
    }
}
