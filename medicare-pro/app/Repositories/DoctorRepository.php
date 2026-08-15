<?php

namespace App\Repositories;

use App\Models\Doctor;
use App\Repositories\Interfaces\DoctorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class DoctorRepository extends BaseRepository implements DoctorRepositoryInterface
{
    public function __construct(Doctor $model)
    {
        parent::__construct($model);
    }

    public function getAvailable(array $relations = [], array $sortBy = []): Collection
    {
        $filters = ['is_available' => true];

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'rating', 'desc'));
    }

    public function getByDepartment(
        int $departmentId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['department_id'] = $departmentId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'rating', 'desc'));
    }

    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->whereHas('department', fn (Builder $q) => $q->where('hospital_id', $hospitalId))
            ->when(!empty($filters), function (Builder $q) use ($filters) {
                foreach ($filters as $column => $condition) {
                    if (is_array($condition) && isset($condition['operator'], $condition['value'])) {
                        $q->where($column, $condition['operator'], $condition['value']);
                    } elseif (is_array($condition) && isset($condition['in'])) {
                        $q->whereIn($column, $condition['in']);
                    } else {
                        $q->where($column, $condition);
                    }
                }
            })
            ->when(!empty($sortBy), function (Builder $q) use ($sortBy) {
                foreach ($sortBy as $column => $direction) {
                    $q->orderBy(is_int($column) ? $direction : $column, is_int($column) ? 'asc' : $direction);
                }
            }, function (Builder $q) {
                $q->orderBy('rating', 'desc');
            })
            ->get();
    }

    public function getBySpecialty(string $specialty, array $relations = [], array $sortBy = []): Collection
    {
        $filters = ['specialty' => ['like' => "%{$specialty}%"]];

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'rating', 'desc'));
    }

    public function search(string $query, array $relations = [], int $limit = 20): Collection
    {
        $wildcard = "%{$query}%";

        return $this->model
            ->newQuery()
            ->with($relations)
            ->whereHas('user', function (Builder $q) use ($wildcard) {
                $q->where('name', 'like', $wildcard);
            })
            ->orWhere('specialty', 'like', $wildcard)
            ->limit($limit)
            ->get();
    }

    public function getTopRated(int $limit = 10, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->orderByDesc('rating')
            ->orderByDesc('total_reviews')
            ->limit($limit)
            ->get();
    }

    public function getMostExperienced(int $limit = 10, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->orderByDesc('experience_years')
            ->limit($limit)
            ->get();
    }

    public function findByUserId(int $userId, array $relations = []): ?Doctor
    {
        return $this->findOneBy(['user_id' => $userId], $relations);
    }

    public function countAvailableInHospital(int $hospitalId): int
    {
        return $this->model
            ->newQuery()
            ->whereHas('department', fn (Builder $q) => $q->where('hospital_id', $hospitalId))
            ->where('is_available', true)
            ->count();
    }

    public function countInDepartment(int $departmentId): int
    {
        return $this->count(['department_id' => $departmentId]);
    }

    public function getAvailableAtDateTime(
        string $date,
        string $time,
        int $departmentId,
        array $relations = []
    ): Collection {
        $dayName = strtolower(now()->parse($date)->englishDayOfWeek);

        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('department_id', $departmentId)
            ->where('is_available', true)
            ->whereRaw("JSON_EXTRACT(schedule_settings, '$.{$dayName}.is_available') = true")
            ->whereRaw("JSON_EXTRACT(schedule_settings, '$.{$dayName}.start_time') <= ?", [$time])
            ->whereRaw("JSON_EXTRACT(schedule_settings, '$.{$dayName}.end_time') > ?", [$time])
            ->get();
    }

    public function paginateByHospital(
        int $hospitalId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $query = $this->model
            ->newQuery()
            ->with($relations)
            ->whereHas('department', fn (Builder $q) => $q->where('hospital_id', $hospitalId));

        $query = $this->applyCriteriaToQuery($query, [], $filters, $this->resolveSortBy($sortBy, 'rating', 'desc'));

        return $query->paginate($perPage);
    }

    public function paginateByDepartment(
        int $departmentId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $filters['department_id'] = $departmentId;

        return $this->paginate($perPage, $relations, $filters, $this->resolveSortBy($sortBy, 'rating', 'desc'));
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
