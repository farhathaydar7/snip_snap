<?php

namespace App\Repositories;

use App\Models\Snippet;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SnippetRepository implements SnippetRepositoryInterface
{
    /**
     * @var Snippet
     */
    protected $model;

    /**
     * SnippetRepository constructor.
     *
     * @param Snippet $snippet
     */
    public function __construct(Snippet $snippet)
    {
        $this->model = $snippet;
    }

    /**
     * Get all snippets with optional filtering
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with('tags')->where('user_id', Auth::id());

        // Filter by title, description, or tags
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('tags', function($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by language
        if (isset($filters['language'])) {
            $query->where('language', $filters['language']);
        }

        // Filter by favorite status
        if (isset($filters['is_favorite'])) {
            $query->where('is_favorite', filter_var($filters['is_favorite'], FILTER_VALIDATE_BOOLEAN));
        }

        // Filter by tag
        if (isset($filters['tag'])) {
            $query->whereHas('tags', function($q) use ($filters) {
                $q->where('name', $filters['tag']);
            });
        }

        // Sort by a specific field
        if (isset($filters['sort'])) {
            $direction = isset($filters['direction']) && strtolower($filters['direction']) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($filters['sort'], $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Find snippet by ID
     * 
     * @param int $id
     * @return Snippet|null
     */
    public function findById(int $id): ?Snippet
    {
        return $this->model->with('tags')->find($id);
    }

    /**
     * Create a new snippet
     * 
     * @param array $data
     * @return Snippet
     */
    public function create(array $data): Snippet
    {
        $snippet = $this->model->create([
            'user_id' => Auth::id(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'code' => $data['code'],
            'language' => $data['language'],
            'is_favorite' => $data['is_favorite'] ?? false,
        ]);

        return $snippet;
    }

    /**
     * Update an existing snippet
     * 
     * @param int $id
     * @param array $data
     * @return Snippet|null
     */
    public function update(int $id, array $data): ?Snippet
    {
        $snippet = $this->findById($id);
        
        if (!$snippet || $snippet->user_id !== Auth::id()) {
            return null;
        }

        $snippet->update($data);
        
        return $snippet;
    }

    /**
     * Create or update a snippet
     * 
     * @param array $data
     * @param int|null $id
     * @return Snippet
     */
    public function createOrUpdate(array $data, ?int $id = null): Snippet
    {
        if ($id) {
            $snippet = $this->findById($id);
            
            if ($snippet && $snippet->user_id === Auth::id()) {
                $snippet->update([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? $snippet->description,
                    'code' => $data['code'],
                    'language' => $data['language'],
                    'is_favorite' => $data['is_favorite'] ?? $snippet->is_favorite,
                ]);
                
                return $snippet;
            }
        }
        
        // If no snippet exists or user doesn't own it, create a new one
        return $this->create($data);
    }

    /**
     * Delete a snippet
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $snippet = $this->findById($id);
        
        if (!$snippet || $snippet->user_id !== Auth::id()) {
            return false;
        }

        return $snippet->delete();
    }

    /**
     * Toggle favorite status
     * 
     * @param int $id
     * @return Snippet|null
     */
    public function toggleFavorite(int $id): ?Snippet
    {
        $snippet = $this->findById($id);
        
        if (!$snippet || $snippet->user_id !== Auth::id()) {
            return null;
        }

        $snippet->is_favorite = !$snippet->is_favorite;
        $snippet->save();
        
        return $snippet;
    }
} 