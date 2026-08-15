<?php

namespace App\Repositories;

use App\Models\Department;
use App\Repositories\Interfaces\DepartmentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    public function __construct(Department $model)
    {
        parent::__construct($model);
    }

    public function getActive(array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['status' => 'active'], $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function getByHospital(
        int $hospitalId,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Collection {
        $filters['hospital_id'] = $hospitalId;

        return $this->all($relations, $filters, $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function findByNameAndHospital(string $name, int $hospitalId, array $relations = []): ?Department
    {
        return $this->findOneBy(['name' => $name, 'hospital_id' => $hospitalId], $relations);
    }

    public function getByStatus(string $status, array $relations = [], array $sortBy = []): Collection
    {
        return $this->all($relations, ['status' => $status], $this->resolveSortBy($sortBy, 'name', 'asc'));
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
            ->where('name', 'like', $wildcard)
            ->orWhere('description', 'like', $wildcard);

        if ($hospitalId !== null) {
            $builder->where('hospital_id', $hospitalId);
        }

        return $builder->limit($limit)->get();
    }

    public function getWithAvailableDoctors(int $hospitalId, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('hospital_id', $hospitalId)
            ->where('status', 'active')
            ->whereHas('doctors', fn (Builder $q) => $q->where('is_available', true))
            ->withCount([
                'doctors' => fn (Builder $q) => $q->where('is_available', true),
            ])
            ->orderBy('name')
            ->get();
    }

    public function getWithDoctorCounts(int $hospitalId, array $relations = []): Collection
    {
        return $this->model
            ->newQuery()
            ->with($relations)
            ->where('hospital_id', $hospitalId)
            ->withCount('doctors')
            ->orderBy('name')
            ->get();
    }

    public function getWithTodayAppointmentCounts(int $hospitalId): Collection
    {
        return $this->model
            ->newQuery()
            ->where('hospital_id', $hospitalId)
            ->withCount([
                'appointments' => fn (Builder $q) => $q->whereDate('appointment_date', now()->toDateString()),
            ])
            ->orderBy('name')
            ->get();
    }

    public function countByHospital(int $hospitalId): int
    {
        return $this->count(['hospital_id' => $hospitalId]);
    }

    public function countActiveByHospital(int $hospitalId): int
    {
        return $this->count(['hospital_id' => $hospitalId, 'status' => 'active']);
    }

    public function paginateByHospital(
        int $hospitalId,
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $filters['hospital_id'] = $hospitalId;

        return $this->paginate($perPage, $relations, $filters, $this->resolveSortBy($sortBy, 'name', 'asc'));
    }

    public function nameExistsInHospital(string $name, int $hospitalId, ?int $excludeId = null): bool
    {
        $query = $this->model
            ->newQuery()
            ->where('name', $name)
            ->where('hospital_id', $hospitalId);

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
