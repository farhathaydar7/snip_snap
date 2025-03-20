<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TagRepository implements TagRepositoryInterface
{
    /**
     * @var Tag
     */
    protected $model;

    /**
     * TagRepository constructor.
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->model = $tag;
    }

    /**
     * Get all tags
     * 
     * @param array $filters
     * @return Collection
     */
    public function getAll(array $filters = []): Collection
    {
        $query = $this->model->withCount(['snippets' => function($query) {
            $query->where('user_id', Auth::id());
        }]);

        // Filter by tags with snippets from the authenticated user
        $query->whereHas('snippets', function($q) {
            $q->where('user_id', Auth::id());
        });

        // Search by tag name
        if (isset($filters['search'])) {
            $query->where('name', 'LIKE', "%{$filters['search']}%");
        }

        // Sort by name or snippet count
        if (isset($filters['sort'])) {
            $direction = isset($filters['direction']) && strtolower($filters['direction']) === 'asc' ? 'asc' : 'desc';
            
            if ($filters['sort'] === 'snippets_count') {
                $query->orderBy('snippets_count', $direction);
            } else {
                $query->orderBy($filters['sort'], $direction);
            }
        } else {
            $query->orderBy('name', 'asc');
        }

        return $query->get();
    }

    /**
     * Find tag by ID
     * 
     * @param int $id
     * @return Tag|null
     */
    public function findById(int $id): ?Tag
    {
        return $this->model->with(['snippets' => function($query) {
            $query->where('user_id', Auth::id());
        }])->find($id);
    }

    /**
     * Find or create a tag by name
     * 
     * @param string $name
     * @return Tag
     */
    public function findOrCreateByName(string $name): Tag
    {
        return $this->model->firstOrCreate(['name' => $name]);
    }

    /**
     * Create a new tag
     * 
     * @param array $data
     * @return Tag
     */
    public function create(array $data): Tag
    {
        return $this->model->create([
            'name' => $data['name'],
        ]);
    }

    /**
     * Update a tag
     * 
     * @param int $id
     * @param array $data
     * @return Tag|null
     */
    public function update(int $id, array $data): ?Tag
    {
        $tag = $this->findById($id);
        
        if (!$tag) {
            return null;
        }

        // Check if user is authorized to update this tag
        $hasSnippetsWithTag = $tag->snippets->isNotEmpty();
        if (!$hasSnippetsWithTag) {
            return null;
        }

        $tag->update([
            'name' => $data['name'],
        ]);
        
        return $tag;
    }

    /**
     * Delete a tag
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $tag = $this->findById($id);
        
        if (!$tag) {
            return false;
        }

        // Check if user is authorized to delete this tag
        // Only allow deleting if this tag is only used by the authenticated user's snippets
        $hasOtherUsersSnippets = $tag->snippets()->where('user_id', '!=', Auth::id())->exists();
        if ($hasOtherUsersSnippets) {
            return false;
        }

        // Detach tag from all of the user's snippets before deletion
        $tag->snippets()->where('user_id', Auth::id())->detach();
        
        // If tag is not used by any other snippets, delete it
        if ($tag->snippets()->count() === 0) {
            $tag->delete();
            return true;
        }
        
        return true;
    }
} 