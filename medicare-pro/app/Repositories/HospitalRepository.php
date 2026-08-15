<?php

namespace App\Repositories;

use App\Models\Hospital;
use App\Repositories\Interfaces\HospitalRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class HospitalRepository extends BaseRepository implements HospitalRepositoryInterface
{
    public function __construct(Hospital $model)
    {
        parent::__construct($model);
    }

    public function getActive(array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['status' => 'active'], $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function findByEmail(string $email, array $relations = []): ?Hospital
    {
        return $this->findOneBy(['email' => $email], $relations);
    }

    public function findByPhone(string $phone, array $relations = []): ?Hospital
    {
        return $this->findOneBy(['phone' => $phone], $relations);
    }

    public function search(string $query, array $relations = [], int $limit = 20): Collection
    {
        $wildcard = "%{$query}%";

        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('name', 'like', $wildcard)
            ->orWhere('address', 'like', $wildcard)
            ->orWhere('phone', 'like', $wildcard)
            ->limit($limit)
            ->get();
    }

    public function getNearby(
        float $latitude,
        float $longitude,
        float $radiusKm = 50,
        array $relations = [],
        int $limit = 20
    ): Collection {
        // Haversine formula — computes distance in km between two lat/lng points.
        // MySQL / SQLite compatible.
        $haversine = sprintf(
            '(6371 * acos(cos(radians(%f)) * cos(radians(latitude)) * cos(radians(longitude) - radians(%f)) + sin(radians(%f)) * sin(radians(latitude))))',
            $latitude,
            $longitude,
            $latitude
        );

        return $this->model
            ->newQuery()
            ->with($relations)
            ->select()
            ->selectRaw("{$haversine} AS distance")
            ->where('status', 'active')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance')
            ->limit($limit)
            ->get();
    }

    public function getByStatus(string $status, array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['status' => $status], $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function getExpiringSubscriptions(int $days = 30, array $relations = []): Collection
    {
        $threshold = now()->addDays($days)->toDateTimeString();

        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('status', 'active')
            ->whereNotNull('subscription_expires_at')
            ->where('subscription_expires_at', '<=', $threshold)
            ->orderBy('subscription_expires_at', 'asc')
            ->get();
    }

    public function getBySubscriptionPlan(int $planId, array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['subscription_plan_id' => $planId], $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function getByLanguage(string $language, array $relations = []): Collection
    {
        return $this->all($relations, ['default_language' => $language]);
    }

    public function countActive(): int
    {
        return $this->count(['status' => 'active']);
    }

    public function getWithStats(array $relations = [], array $sortBy = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->withCount(['departments', 'doctors', 'users'])
            ->when(!empty($sortBy), function ($q) use ($sortBy) {
                foreach ($sortBy as $column => $direction) {
                    $q->orderBy(is_int($column) ? $direction : $column, is_int($column) ? 'asc' : $direction);
                }
            }, fn ($q) => $q->orderBy('name'))
            ->get();
    }

    public function paginateWithFilters(
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        return $this->paginate($perPage, $relations, $filters, $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $query = $this->model->newQuery()->where('name', $name);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /* ------------------------------------------------------------------
     |  Type-safe overrides
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
