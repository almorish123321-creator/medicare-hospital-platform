<?php

namespace App\Repositories\Interfaces;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface extends RepositoryInterface
{
    /**
     * Find a patient by their user ID.
     */
    public function findByUserId(int $userId, array $relations = []): ?Patient;

    /**
     * Get patients by hospital (through users).
     */
    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection;

    /**
     * Search patients by name, phone, or patient ID.
     */
    public function search(
        string $query,
        ?int $hospitalId = null,
        array $relations = [],
        int $limit = 20
    ): Collection;

    /**
     * Get patients by blood type.
     */
    public function getByBloodType(
        string $bloodType,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get patients by gender.
     */
    public function getByGender(
        string $gender,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get patients whose birthday falls within a given date range.
     */
    public function getByBirthDateRange(
        string $startDate,
        string $endDate,
        array $relations = []
    ): Collection;

    /**
     * Get patients with a specific allergy.
     */
    public function getByAllergy(string $allergy, array $relations = []): Collection;

    /**
     * Get patients with a specific chronic disease.
     */
    public function getByChronicDisease(string $disease, array $relations = []): Collection;

    /**
     * Get the total patient count for a hospital.
     */
    public function countByHospital(int $hospitalId): int;

    /**
     * Get new patients registered within a date range.
     */
    public function getNewPatients(
        string $startDate,
        string $endDate,
        ?int $hospitalId = null,
        array $relations = []
    ): Collection;

    /**
     * Get recent patients (ordered by creation date).
     */
    public function getRecent(int $limit = 10, array $relations = []): Collection;

    /**
     * Paginate patients by hospital.
     */
    public function paginateByHospital(
        int $hospitalId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Check if a patient profile exists for a given user ID.
     */
    public function existsForUser(int $userId): bool;
}
