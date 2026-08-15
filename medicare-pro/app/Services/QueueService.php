<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\QueueLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class QueueService
{
    public function generateQueueNumber(Department $department): int
    {
        $cacheKey = "queue_counter_{$department->id}_" . now()->toDateString();
        return (int) Cache::lock($cacheKey, 10)->get(function () use ($cacheKey, $department) {
            $lastNumber = Cache::get($cacheKey, 0);
            $newNumber = $lastNumber + 1;
            Cache::put($cacheKey, $newNumber, now()->endOfDay());
            return $newNumber;
        });
    }

    public function assignQueueNumber(Appointment $appointment): Appointment
    {
        $department = Department::find($appointment->department_id);
        $queueNumber = $this->generateQueueNumber($department);

        $appointment->update(['queue_number' => $queueNumber]);

        QueueLog::create([
            'appointment_id' => $appointment->id,
            'queue_number' => $queueNumber,
            'status' => 'waiting',
            'estimated_wait_time' => $this->estimateWaitTime($department, $queueNumber),
        ]);

        return $appointment;
    }

    public function callNext(int $departmentId): ?Appointment
    {
        $nextAppointment = Appointment::where('department_id', $departmentId)
            ->whereDate('appointment_date', now()->toDateString())
            ->where('status', 'checked_in')
            ->whereNotNull('queue_number')
            ->orderBy('queue_number')
            ->first();

        if ($nextAppointment) {
            $nextAppointment->update(['status' => 'in_progress', 'started_at' => now()]);
            $nextAppointment->queueLog->update([
                'status' => 'in_progress',
                'called_at' => now(),
            ]);
        }

        return $nextAppointment;
    }

    public function skipCurrent(int $departmentId): ?Appointment
    {
        $currentAppointment = Appointment::where('department_id', $departmentId)
            ->whereDate('appointment_date', now()->toDateString())
            ->where('status', 'in_progress')
            ->whereNotNull('queue_number')
            ->orderBy('queue_number')
            ->first();

        if ($currentAppointment) {
            $currentAppointment->update(['status' => 'checked_in']);
            $currentAppointment->queueLog->update(['status' => 'skipped']);
        }

        return $currentAppointment;
    }

    public function estimateWaitTime(Department $department, int $queueNumber): int
    {
        $today = now()->toDateString();
        $completedToday = QueueLog::whereHas('appointment', function ($q) use ($department, $today) {
            $q->where('department_id', $department->id)->whereDate('appointment_date', $today);
        })->where('status', 'completed')->count();

        $avgConsultationTime = $completedToday > 0
            ? (int) Cache::remember("avg_consultation_{$department->id}", 3600, function () use ($department, $today) {
                $completed = QueueLog::whereHas('appointment', function ($q) use ($department, $today) {
                    $q->where('department_id', $department->id)->whereDate('appointment_date', $today);
                })->where('status', 'completed')->get();

                if ($completed->isEmpty()) return 15;
                $totalMinutes = $completed->sum(function ($log) {
                    return $log->called_at && $log->created_at
                        ? $log->called_at->diffInMinutes($log->created_at)
                        : 15;
                });
                return (int) ($totalMinutes / $completed->count()) ?: 15;
            })
            : 15;

        $waitingCount = QueueLog::whereHas('appointment', function ($q) use ($department, $today) {
            $q->where('department_id', $department->id)->whereDate('appointment_date', $today);
        })->where('status', 'waiting')->count();

        return $waitingCount * $avgConsultationTime;
    }

    public function getQueueStatus(int $departmentId): array
    {
        $today = now()->toDateString();
        $current = Appointment::where('department_id', $departmentId)
            ->whereDate('appointment_date', $today)
            ->where('status', 'in_progress')
            ->first();

        $waiting = Appointment::where('department_id', $departmentId)
            ->whereDate('appointment_date', $today)
            ->whereIn('status', ['checked_in', 'pending'])
            ->whereNotNull('queue_number')
            ->orderBy('queue_number')
            ->take(5)
            ->get();

        $completed = QueueLog::whereHas('appointment', function ($q) use ($departmentId, $today) {
            $q->where('department_id', $departmentId)->whereDate('appointment_date', $today);
        })->where('status', 'completed')->count();

        return [
            'current_serving' => $current ? $current->getQueueDisplayNumber() : null,
            'next_patients' => $waiting->map(fn($a) => [
                'queue_number' => $a->getQueueDisplayNumber(),
                'patient' => $a->patient->user->name,
            ]),
            'completed_today' => $completed,
            'average_wait_time' => $this->estimateWaitTime(
                Department::find($departmentId), 1
            ),
            'waiting_count' => $waiting->count(),
        ];
    }

    public function resetDailyQueues(): void
    {
        // Clear all queue counters from cache
        // The daily cache keys will auto-expire at end of day
    }
}
