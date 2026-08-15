<?php

namespace App\Repositories;

use App\Models\Patient;
use App\Repositories\Interfaces\PatientRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository extends BaseRepository implements PatientRepositoryInterface
{
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function findByUserId(int $userId, array $relations = []): ?Patient
    {
        return $this->findOneBy(['user_id' => $userId], $relations);
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
            ->whereHas('user', fn (Builder $q) => $q->where('hospital_id', $hospitalId))
            ->when(!empty($filters), function (Builder $q) use ($filters) {
                foreach ($filters as $column => $condition) {
                    if (is_array($condition) && isset($condition['operator'], $condition['value'])) {
                        $q->where($column, $condition['operator'], $condition['value']);
                    } elseif (is_array($condition) && isset($condition['in'])) {
                        $q->whereIn($column, $condition['in']);
                    } elseif (is_array($condition) && isset($condition['like'])) {
                        $q->where($column, 'like', $condition['like']);
                    } elseif (is_array($condition) && isset($condition['between'])) {
                        $q->whereBetween($column, $condition['between']);
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
                $q->orderByDesc('created_at');
            })
            ->get();
    }

    public function search(
        string $query,
        ?int $hospitalId = null,
        array $relations = [],
        int $limit = 20
    ): Collection {
        $wildcard = "%{$query}%";

        $builder = $this->model
            ->newQuery()
            ->with($relations)
            ->whereHas('user', function (Builder $q) use ($wildcard) {
                $q->where('name', 'like', $wildcard)
                  ->orWhere('phone', 'like', $wildcard);
            });

        if ($hospitalId !== null) {
            $builder->whereHas('user', fn (Builder $q) => $q->where('hospital_id', $hospitalId));
        }

        return $builder->limit($limit)->get();
    }

    public function getByBloodType(string $bloodType, array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['blood_type' => $bloodType], $this->resolveSortBy($sortBy));
    }

    public function getByGender(string $gender, array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['gender' => $gender], $this->resolveSortBy($sortBy));
    }

    public function getByBirthDateRange(string $startDate, string $endDate, array $relations = []): Collection
    {
        return $this->all($relations, [
            'date_of_birth' => ['between' => [$startDate, $endDate]],
        ]);
    }

    public function getByAllergy(string $allergy, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('allergies', 'like', "%{$allergy}%")
            ->get();
    }

    public function getByChronicDisease(string $disease, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('chronic_diseases', 'like', "%{$disease}%")
            ->get();
    }

    public function countByHospital(int $hospitalId): int
    {
        return $this->model
            ->newQuery()
            ->whereHas('user', fn (Builder $q) => $q->where('hospital_id', $hospitalId))
            ->count();
    }

    public function getNewPatients(
        string $startDate,
        string $endDate,
        ?int $hospitalId = null,
        array $relations = []
    ): Collection {
        $builder = $this->model
            ->newQuery()
            ->with($relations)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if ($hospitalId !== null) {
            $builder->whereHas('user', fn (Builder $q) => $q->where('hospital_id', $hospitalId));
        }

        return $builder->orderByDesc('created_at')->get();
    }

    public function getRecent(int $limit = 10, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->orderByDesc('created_at')
            ->limit($limit)
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
            ->whereHas('user', fn (Builder $q) => $q->where('hospital_id', $hospitalId));

        $query = $this->applyCriteriaToQuery($query, [], $filters, $this->resolveSortBy($sortBy));

        return $query->paginate($perPage);
    }

    public function existsForUser(int $userId): bool
    {
        return $this->exists(['user_id' => $userId]);
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
