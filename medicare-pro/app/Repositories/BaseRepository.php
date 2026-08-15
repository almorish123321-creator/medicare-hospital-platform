<?php

namespace App\Repositories;

use App\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Builder $query;

    public function __construct(
        protected Model $model
    ) {
        $this->query = $this->model->newQuery();
    }

    /**
     * Reset the internal query builder to a fresh instance.
     */
    protected function resetQuery(): static
    {
        $this->query = $this->model->newQuery();

        return $this;
    }

    /**
     * Apply relations, filters, and sorting to the query builder.
     */
    protected function applyCriteriaToQuery(
        Builder $query,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): Builder {
        // Eager-load relations
        if (!empty($relations)) {
            $query->with($relations);
        }

        // Apply filters (column => value or column => ['operator' => ..., 'value' => ...])
        foreach ($filters as $column => $condition) {
            if (is_array($condition) && isset($condition['operator'], $condition['value'])) {
                $query->where($column, $condition['operator'], $condition['value']);
            } elseif (is_array($condition) && isset($condition['in'])) {
                $query->whereIn($column, $condition['in']);
            } elseif (is_array($condition) && isset($condition['not_in'])) {
                $query->whereNotIn($column, $condition['not_in']);
            } elseif (is_array($condition) && isset($condition['null']) && $condition['null'] === true) {
                $query->whereNull($column);
            } elseif (is_array($condition) && isset($condition['not_null']) && $condition['not_null'] === true) {
                $query->whereNotNull($column);
            } elseif (is_array($condition) && isset($condition['like'])) {
                $query->where($column, 'like', $condition['like']);
            } elseif (is_array($condition) && isset($condition['between'])) {
                $query->whereBetween($column, $condition['between']);
            } elseif (is_array($condition) && isset($condition['date'])) {
                $query->whereDate($column, $condition['date']);
            } else {
                $query->where($column, $condition);
            }
        }

        // Apply sorting
        foreach ($sortBy as $column => $direction) {
            if (is_int($column)) {
                // e.g. ['created_at'] → default asc
                $query->orderBy($direction, 'asc');
            } else {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * Resolve a sort array — apply a default if none provided.
     */
    protected function resolveSortBy(array $sortBy, string $defaultColumn = 'created_at', string $defaultDirection = 'desc'): array
    {
        return empty($sortBy) ? [$defaultColumn => $defaultDirection] : $sortBy;
    }

    /* ------------------------------------------------------------------
     |  RepositoryInterface Implementation
     | ------------------------------------------------------------------ */

    public function all(array $relations = [], array $filters = [], array $sortBy = []): Collection
    {
        $sortBy = $this->resolveSortBy($sortBy);
        $query = $this->applyCriteriaToQuery($this->query, $relations, $filters, $sortBy);
        $results = $query->get();
        $this->resetQuery();

        return $results;
    }

    public function find(int $id, array $relations = []): ?Model
    {
        $model = $this->query->with($relations)->find($id);
        $this->resetQuery();

        return $model;
    }

    public function findOrFail(int $id, array $relations = []): Model
    {
        $model = $this->query->with($relations)->findOrFail($id);
        $this->resetQuery();

        return $model;
    }

    public function findOneBy(array $criteria, array $relations = []): ?Model
    {
        $query = $this->query->with($relations);
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }
        $model = $query->first();
        $this->resetQuery();

        return $model;
    }

    public function findOneByOrFail(array $criteria, array $relations = []): Model
    {
        $query = $this->query->with($relations);
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }
        $model = $query->firstOrFail();
        $this->resetQuery();

        return $model;
    }

    public function findBy(array $criteria, array $relations = [], array $sortBy = []): Collection
    {
        $sortBy = $this->resolveSortBy($sortBy);
        $query = $this->applyCriteriaToQuery($this->query, $relations, [], $sortBy);
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }
        $results = $query->get();
        $this->resetQuery();

        return $results;
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes): Model
    {
        $model = $this->model->findOrFail($id);
        $model->update($attributes);

        return $model->fresh();
    }

    public function updateWhere(array $criteria, array $attributes): int
    {
        $query = $this->model->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->update($attributes);
    }

    public function delete(int $id): bool
    {
        $model = $this->model->findOrFail($id);

        return $model->delete();
    }

    public function deleteWhere(array $criteria): int
    {
        $query = $this->model->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->delete();
    }

    public function paginate(
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator {
        $sortBy = $this->resolveSortBy($sortBy);
        $query = $this->applyCriteriaToQuery($this->query, $relations, $filters, $sortBy);
        $results = $query->paginate($perPage);
        $this->resetQuery();

        return $results;
    }

    public function with(array $relations): static
    {
        $this->query->with($relations);

        return $this;
    }

    public function where(string $column, mixed $operator, mixed $value = null): static
    {
        $this->query->where($column, $operator, $value);

        return $this;
    }

    public function whereIn(string $column, array $values): static
    {
        $this->query->whereIn($column, $values);

        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): static
    {
        $this->query->orderBy($column, $direction);

        return $this;
    }

    public function applyCriteria(array $relations = [], array $filters = [], array $sortBy = []): static
    {
        $this->applyCriteriaToQuery($this->query, $relations, $filters, $sortBy);

        return $this;
    }

    public function count(array $criteria = []): int
    {
        $query = $this->model->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->count();
    }

    public function exists(array $criteria): bool
    {
        $query = $this->model->newQuery();
        foreach ($criteria as $column => $value) {
            $query->where($column, $value);
        }

        return $query->exists();
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Dynamically pass missing methods to the underlying query builder.
     */
    public function __call(string $method, array $arguments): mixed
    {
        $result = $this->query->$method(...$arguments);
        $this->resetQuery();

        return $result;
    }
}
