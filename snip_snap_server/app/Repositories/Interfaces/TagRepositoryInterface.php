<?php

namespace App\Repositories\Interfaces;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

interface TagRepositoryInterface
{
    /**
     * Get all tags for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getAllTags(int $userId): Collection;

    /**
     * Find a tag by ID.
     *
     * @param int $id
     * @return Tag|null
     */
    public function find(int $id): ?Tag;

    /**
     * Find a tag by name and user ID.
     *
     * @param string $name
     * @param int $userId
     * @return Tag|null
     */
    public function findByName(string $name, int $userId): ?Tag;

    /**
     * Create a new tag.
     *
     * @param array $data
     * @return Tag
     */
    public function create(array $data): Tag;

    /**
     * Update an existing tag.
     *
     * @param int $id
     * @param array $data
     * @return Tag
     */
    public function update(int $id, array $data): Tag;

    /**
     * Delete a tag.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Sync tags for a snippet.
     *
     * @param int $snippetId
     * @param array $tagNames
     * @return void
     */
    public function syncSnippetTags(int $snippetId, array $tagNames): void;
}
