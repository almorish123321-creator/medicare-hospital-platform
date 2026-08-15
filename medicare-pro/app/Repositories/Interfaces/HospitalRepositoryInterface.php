<?php

namespace App\Repositories\Interfaces;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface HospitalRepositoryInterface extends RepositoryInterface
{
    /**
     * Get all active hospitals.
     */
    public function getActive(array $relations = [], array $sortBy = []): Collection;

    /**
     * Find a hospital by its email address.
     */
    public function findByEmail(string $email, array $relations = []): ?Hospital;

    /**
     * Find a hospital by its phone number.
     */
    public function findByPhone(string $phone, array $relations = []): ?Hospital;

    /**
     * Search hospitals by name, address, or phone.
     */
    public function search(
        string $query,
        array $relations = [],
        int $limit = 20
    ): Collection;

    /**
     * Get hospitals near a geographic coordinate.
     */
    public function getNearby(
        float $latitude,
        float $longitude,
        float $radiusKm = 50,
        array $relations = [],
        int $limit = 20
    ): Collection;

    /**
     * Get hospitals by status.
     */
    public function getByStatus(
        string $status,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get hospitals expiring within a given number of days.
     */
    public function getExpiringSubscriptions(int $days = 30, array $relations = []): Collection;

    /**
     * Get hospitals with a specific subscription plan.
     */
    public function getBySubscriptionPlan(
        int $planId,
        array $relations = [],
        array $sortBy = []
    ): Collection;

    /**
     * Get hospitals by default language.
     */
    public function getByLanguage(
        string $language,
        array $relations = []
    ): Collection;

    /**
     * Get the total count of active hospitals.
     */
    public function countActive(): int;

    /**
     * Get hospitals with their department and doctor counts.
     */
    public function getWithStats(array $relations = [], array $sortBy = []): Collection;

    /**
     * Paginate hospitals with optional filters.
     */
    public function paginateWithFilters(
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Check if a hospital name already exists (optionally excluding a given ID).
     */
    public function nameExists(string $name, ?int $excludeId = null): bool;
}