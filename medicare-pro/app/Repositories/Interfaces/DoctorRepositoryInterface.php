<?php

namespace App\Repositories\Interfaces;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface DoctorRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all available doctors.
     */
    public function getAvailable(array $relations = [], array $sortBy = []): Collection;

    /**
     * Get doctors by department.
     */
    public function getByDepartment(
        int $departmentId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get doctors by hospital (through departments).
     */
    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get doctors by specialty.
     */
    public function getBySpecialty(
        string $specialty,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Search doctors by name (via user relation) and/or specialty.
     */
    public function search(
        string $query,
        array $relations = [],
        int $limit = 20
    ): Collection;

    /**
     * Get doctors sorted by rating (highest first).
     */
    public function getTopRated(int $limit = 10, array $relations = []): Collection;

    /**
     * Get doctors with the most experience.
     */
    public function getMostExperienced(int $limit = 10, array $relations = []): Collection;

    /**
     * Find a doctor by their user ID.
     */
    public function findByUserId(int $userId, array $relations = []): ?Doctor;

    /**
     * Get the total count of available doctors in a hospital.
     */
    public function countAvailableInHospital(int $hospitalId): int;

    /**
     * Get the total count of doctors in a department.
     */
    public function countInDepartment(int $departmentId): int;

    /**
     * Get doctors available in a given date/time (checks schedule settings).
     */
    public function getAvailableAtDateTime(
        string $date,
        string $time,
        int $departmentId,
        array $relations = []
    ): Collection;

    /**
     * Paginate doctors by hospital.
     */
    public function paginateByHospital(
        int $hospitalId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Paginate doctors by department.
     */
    public function paginateByDepartment(
        int $departmentId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;
}
