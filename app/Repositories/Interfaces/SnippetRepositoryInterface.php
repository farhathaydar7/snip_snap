<?php

namespace App\Repositories\Interfaces;

use App\Models\Snippet;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface SnippetRepositoryInterface
{
    /**
     * Get all snippets with optional filtering
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator;
    
    /**
     * Find snippet by ID
     * 
     * @param int $id
     * @return Snippet|null
     */
    public function findById(int $id): ?Snippet;
    
    /**
     * Create a new snippet
     * 
     * @param array $data
     * @return Snippet
     */
    public function create(array $data): Snippet;
    
    /**
     * Update an existing snippet
     * 
     * @param int $id
     * @param array $data
     * @return Snippet|null
     */
    public function update(int $id, array $data): ?Snippet;
    
    /**
     * Create or update a snippet
     * 
     * @param array $data
     * @param int|null $id
     * @return Snippet
     */
    public function createOrUpdate(array $data, ?int $id = null): Snippet;
    
    /**
     * Delete a snippet
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
    
    /**
     * Toggle favorite status
     * 
     * @param int $id
     * @return Snippet|null
     */
    public function toggleFavorite(int $id): ?Snippet;
} 