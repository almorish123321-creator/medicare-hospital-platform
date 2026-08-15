<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Repositories\Interfaces\AppointmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class AppointmentRepository extends BaseRepository implements AppointmentRepositoryInterface
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    public function getByDateRange(
        string $startDate,
        string $endDate,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $sortBy = $this->resolveSortBy($sortBy, 'appointment_date', 'asc');
        $filters['appointment_date'] = ['between' => [$startDate, $endDate]];

        return $this->all($relations, $filters, $sortBy);
    }

    public function getByDoctor(
        int $doctorId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['doctor_id'] = $doctorId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function getByPatient(
        int $patientId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['patient_id'] = $patientId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['hospital_id'] = $hospitalId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function getByDepartment(
        int $departmentId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['department_id'] = $departmentId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function getByStatus(string $status, array $relations = [], array $sortBy = []): Collection
    {
        $filters = ['status' => $status];

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function getToday(array $relations = [], array $filters = [], array $sortBy = []): Collection
    {
        $filters['appointment_date'] = ['date' => now()->toDateString()];

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_time', 'asc'));
    }

    public function getTodayForDoctor(int $doctorId, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', now()->toDateString())
            ->orderBy('appointment_time', 'asc')
            ->get();
    }

    public function getTodayForDepartment(int $departmentId, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('department_id', $departmentId)
            ->whereDate('appointment_date', now()->toDateString())
            ->orderBy('queue_number', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->get();
    }

    public function getActive(array $relations = [], array $sortBy = []): Collection
    {
        $filters = ['status' => ['in' => ['confirmed', 'checked_in', 'in_progress']]];

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'asc'));
    }

    public function getUpcoming(array $relations = [], int $limit = 10): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->limit($limit)
            ->get();
    }

    public function getNextForPatient(int $patientId, array $relations = []): ?Appointment
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('patient_id', $patientId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->orderBy('appointment_time', 'asc')
            ->first();
    }

    public function getNextInQueue(int $departmentId, array $relations = []): ?Appointment
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('department_id', $departmentId)
            ->whereDate('appointment_date', now()->toDateString())
            ->whereIn('status', ['checked_in', 'in_progress'])
            ->whereNotNull('queue_number')
            ->orderBy('queue_number', 'asc')
            ->first();
    }

    public function getStatusCountsForDate(string $date, ?int $hospitalId = null): array
    {
        $query = $this->model->newQuery()
            ->select('status', $this->model->raw('COUNT(*) as count'))
            ->whereDate('appointment_date', $date);

        if ($hospitalId !== null) {
            $query->where('hospital_id', $hospitalId);
        }

        return $query->groupBy('status')->pluck('count', 'status')->toArray();
    }

    public function hasTimeConflict(
        int $doctorId,
        string $date,
        string $time,
        ?int $excludeAppointmentId = null
    ): bool {
        $query = $this->model->newQuery()
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in', 'in_progress']);

        if ($excludeAppointmentId !== null) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return $query->exists();
    }

    public function paginateByDoctor(
        int $doctorId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $filters['doctor_id'] = $doctorId;

        return $this->paginate($perPage, $relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    public function paginateByPatient(
        int $patientId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $filters['patient_id'] = $patientId;

        return $this->paginate($perPage, $relations, $filters, $this->resolveSortBy($sortBy, 'appointment_date', 'desc'));
    }

    /* ------------------------------------------------------------------
     |  Override to add the Appointment model to the return type.
     | ------------------------------------------------------------------ */

    public function find(int $id, array $relations = []): ?Model
    {
        return parent::find($id, $relations);
    }

    public function findOrFail(int $id, array $relations = []): Model
    {
        return parent::findOrFail($id, $relations);
    }
}
