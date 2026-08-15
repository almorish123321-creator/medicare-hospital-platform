<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Get all records, optionally with relations, filters, and sorting.
     */
    public function all(array $relations = [], array $filters = [], array $sortBy = []): Collection;

    /**
     * Find a record by its primary key.
     */
    public function find(int $id, array $relations = []): ?Model;

    /**
     * Find a record by its primary key or throw a ModelNotFoundException.
     */
    public function findOrFail(int $id, array $relations = []): Model;

    /**
     * Find a single record matching the given criteria.
     */
    public function findOneBy(array $criteria, array $relations = []): ?Model;

    /**
     * Find a single record or throw if not found.
     */
    public function findOneByOrFail(array $criteria, array $relations = []): Model;

    /**
     * Get all records matching the given criteria.
     */
    public function findBy(array $criteria, array $relations = [], array $sortBy = []): Collection;

    /**
     * Create a new record with the given attributes.
     */
    public function create(array $attributes): Model;

    /**
     * Update a record by its primary key.
     */
    public function update(int $id, array $attributes): Model;

    /**
     * Update records matching the given criteria.
     */
    public function updateWhere(array $criteria, array $attributes): int;

    /**
     * Delete a record by its primary key.
     */
    public function delete(int $id): bool;

    /**
     * Delete records matching the given criteria.
     */
    public function deleteWhere(array $criteria): int;

    /**
     * Paginate records with optional relations, filters, sorting, and per-page count.
     */
    public function paginate(
        int $perPage = 15,
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): LengthAwarePaginator;

    /**
     * Get a query builder instance with eager-loaded relations.
     */
    public function with(array $relations): static;

    /**
     * Add a where clause to the query.
     */
    public function where(string $column, mixed $operator, mixed $value = null): static;

    /**
     * Add a whereIn clause to the query.
     */
    public function whereIn(string $column, array $values): static;

    /**
     * Add an orderBy clause to the query.
     */
    public function orderBy(string $column, string $direction = 'asc'): static;

    /**
     * Apply scopes, filters, sorting, and relations to the underlying query.
     */
    public function applyCriteria(
        array $relations = [],
        array $filters = [],
        array $sortBy = []
    ): static;

    /**
     * Get the count of records matching optional criteria.
     */
    public function count(array $criteria = []): int;

    /**
     * Check if any record matches the given criteria.
     */
    public function exists(array $criteria): bool;

    /**
     * Get the underlying model instance.
     */
    public function getModel(): Model;
}
