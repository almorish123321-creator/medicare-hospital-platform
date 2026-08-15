<?php

namespace App\Repositories\Interfaces;

use App\Models\Appointment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface AppointmentRepositoryInterface extends RepositoryInterface
{
    /**
     * Get appointments within a date range.
     */
    public function getByDateRange(
        string $startDate,
        string $endDate,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get all appointments for a specific doctor.
     */
    public function getByDoctor(
        int $doctorId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get all appointments for a specific patient.
     */
    public function getByPatient(
        int $patientId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get all appointments for a specific hospital.
     */
    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get all appointments for a specific department.
     */
    public function getByDepartment(
        int $departmentId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get appointments by status.
     */
    public function getByStatus(
        string $status,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get today's appointments.
     */
    public function getToday(
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get today's appointments for a specific doctor.
     */
    public function getTodayForDoctor(int $doctorId, array $relations = []): Collection;

    /**
     * Get today's appointments for a specific department.
     */
    public function getTodayForDepartment(int $departmentId, array $relations = []): Collection;

    /**
     * Get active appointments (confirmed, checked_in, or in_progress).
     */
    public function getActive(array $relations = [], array $sortBy = []): Collection;

    /**
     * Get upcoming appointments (after now).
     */
    public function getUpcoming(array $relations = [], int $limit = 10): Collection;

    /**
     * Get the next appointment for a specific patient.
     */
    public function getNextForPatient(int $patientId, array $relations = []): ?Appointment;

    /**
     * Get the next appointment in queue for a specific department.
     */
    public function getNextInQueue(int $departmentId, array $relations = []): ?Appointment;

    /**
     * Get count of appointments grouped by status for a given date.
     */
    public function getStatusCountsForDate(string $date, ?int $hospitalId = null): array;

    /**
     * Check if a doctor has a time-slot conflict.
     */
    public function hasTimeConflict(
        int $doctorId,
        string $date,
        string $time,
        ?int $excludeAppointmentId = null
    ): bool;

    /**
     * Paginate appointments for a doctor.
     */
    public function paginateByDoctor(
        int $doctorId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Paginate appointments for a patient.
     */
    public function paginateByPatient(
        int $patientId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;
}
