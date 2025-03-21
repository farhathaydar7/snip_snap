<?php

namespace App\Repositories;

use App\Models\Snippet;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SnippetRepository implements SnippetRepositoryInterface
{
    /**
     * Get all snippets with optional filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSnippets(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Snippet::query();

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['search']) && !empty($filters['search'])) {
            // Log the search term for debugging
            \Log::debug('Search term: ' . $filters['search']);

            $search = '%' . str_replace(['%', '_'], ['\%', '\_'], $filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('code', 'like', $search)
                    ->orWhere('language', 'like', $search)
                    ->orWhereHas('tags', function ($tagQuery) use ($search) {
                        $tagQuery->where('name', 'like', $search);
                    });
            });
        }

        if (isset($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        if (isset($filters['is_favorite'])) {
            $query->where('is_favorite', $filters['is_favorite']);
        }

        if (isset($filters['tag']) && !empty($filters['tag'])) {
            // Log the tag term for debugging
            \Log::debug('Tag search term: ' . $filters['tag']);

            $tag = '%' . str_replace(['%', '_'], ['\%', '\_'], $filters['tag']) . '%';
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', 'like', $tag);
            });
        }

        // Apply sorting
        $sort = $filters['sort'] ?? 'created_at';
        $direction = $filters['direction'] ?? 'desc';
        $query->orderBy($sort, $direction);

        // Eager load tags
        $query->with('tags');

        return $query->paginate($perPage);
    }

    /**
     * Find a snippet by ID.
     *
     * @param int $id
     * @return Snippet|null
     */
    public function find(int $id): ?Snippet
    {
        return Snippet::with('tags')->find($id);
    }

    /**
     * Create a new snippet.
     *
     * @param array $data
     * @return Snippet
     */
    public function create(array $data): Snippet
    {
        return Snippet::create($data);
    }

    /**
     * Update an existing snippet.
     *
     * @param int $id
     * @param array $data
     * @return Snippet
     */
    public function update(int $id, array $data): Snippet
    {
        $snippet = $this->find($id);
        $snippet->update($data);
        return $snippet->fresh();
    }

    /**
     * Delete a snippet.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $snippet = $this->find($id);
        return $snippet ? $snippet->delete() : false;
    }
}
