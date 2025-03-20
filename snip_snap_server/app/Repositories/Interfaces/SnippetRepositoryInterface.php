<?php

namespace App\Repositories\Interfaces;

use App\Models\Snippet;
use Illuminate\Pagination\LengthAwarePaginator;

interface SnippetRepositoryInterface
{
    /**
     * Get all snippets with optional filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSnippets(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Find a snippet by ID.
     *
     * @param int $id
     * @return Snippet|null
     */
    public function find(int $id): ?Snippet;

    /**
     * Create a new snippet.
     *
     * @param array $data
     * @return Snippet
     */
    public function create(array $data): Snippet;

    /**
     * Update an existing snippet.
     *
     * @param int $id
     * @param array $data
     * @return Snippet
     */
    public function update(int $id, array $data): Snippet;

    /**
     * Delete a snippet.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
}
