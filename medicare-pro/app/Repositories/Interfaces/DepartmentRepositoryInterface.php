<?php

namespace App\Repositories\Interfaces;

use App\Models\Department;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DepartmentRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all active departments.
     */
    public function getActive(array $relations = [], array $sortBy = []): Collection;

    /**
     * Get departments belonging to a specific hospital.
     */
    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Find a department by its name and hospital.
     */
    public function findByNameAndHospital(string $name, int $hospitalId, array $relations = []): ?Department;

    /**
     * Get departments by status.
     */
    public function getByStatus(
        string $status,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Search departments by name or description.
     */
    public function search(
        string $query,
        ?int $hospitalId = null,
        array $relations = [],
        int $limit = 20
    ): Collection;

    /**
     * Get departments that have at least one available doctor.
     */
    public function getWithAvailableDoctors(int $hospitalId, array $relations = []): Collection;

    /**
     * Get the doctor count for each department in a hospital.
     */
    public function getWithDoctorCounts(int $hospitalId, array $relations = []): Collection;

    /**
     * Get today's appointment counts per department for a hospital.
     */
    public function getWithTodayAppointmentCounts(int $hospitalId): Collection;

    /**
     * Get the total count of departments in a hospital.
     */
    public function countByHospital(int $hospitalId): int;

    /**
     * Get the total count of active departments in a hospital.
     */
    public function countActiveByHospital(int $hospitalId): int;

    /**
     * Paginate departments by hospital.
     */
    public function paginateByHospital(
        int $hospitalId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Check if a department name already exists in a hospital (optionally excluding a given ID).
     */
    public function nameExistsInHospital(string $name, int $hospitalId, ?int $excludeId = null): bool;
}
