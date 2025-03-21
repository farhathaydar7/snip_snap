<?php

namespace App\Repositories\Interfaces;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface
{
    /**
     * Get all tags
     * 
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection;
    
    /**
     * Find tag by ID
     * 
     * @param int $id
     * @return Tag|null
     */
    public function findById(int $id): ?Tag;
    
    /**
     * Find or create a tag by name
     * 
     * @param string $name
     * @return Tag
     */
    public function findOrCreateByName(string $name): Tag;
    
    /**
     * Create a new tag
     * 
     * @param array $data
     * @return Tag
     */
    public function create(array $data): Tag;
    
    /**
     * Update a tag
     * 
     * @param int $id
     * @param array $data
     * @return Tag|null
     */
    public function update(int $id, array $data): ?Tag;
    
    /**
     * Delete a tag
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;
} 