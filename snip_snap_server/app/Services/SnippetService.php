<?php

namespace App\Services;

use App\DTO\SnippetDTO;
use App\Models\Snippet;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SnippetService
{
    protected $snippetRepository;
    protected $tagRepository;

    /**
     * SnippetService constructor.
     *
     * @param SnippetRepositoryInterface $snippetRepository
     * @param TagRepositoryInterface $tagRepository
     */
    public function __construct(
        SnippetRepositoryInterface $snippetRepository,
        TagRepositoryInterface $tagRepository
    ) {
        $this->snippetRepository = $snippetRepository;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get all snippets with optional filtering.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSnippets(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        // Add user ID to filters to ensure we only get the current user's snippets
        $filters['user_id'] = Auth::id();

        return $this->snippetRepository->getAllSnippets($filters, $perPage);
    }

    /**
     * Get a specific snippet by ID.
     *
     * @param int $id
     * @return Snippet|null
     */
    public function getSnippet(int $id): ?Snippet
    {
        return $this->snippetRepository->find($id);
    }

    /**
     * Create or update a snippet.
     *
     * @param SnippetDTO $snippetDTO
     * @return Snippet
     */
    public function createOrUpdateSnippet(SnippetDTO $snippetDTO): Snippet
    {
        try {
            DB::beginTransaction();

            // If ID is provided and snippet exists, update it, otherwise create new
            $snippet = null;
            if ($snippetDTO->id) {
                $snippet = $this->snippetRepository->find($snippetDTO->id);
                // Check if snippet exists and belongs to current user
                if (!$snippet || $snippet->user_id !== Auth::id()) {
                    throw new \Exception('Snippet not found or unauthorized');
                }
            }

            // Create or update the snippet
            $snippetData = [
                'title' => $snippetDTO->title,
                'description' => $snippetDTO->description,
                'code' => $snippetDTO->code,
                'language' => $snippetDTO->language,
                'is_favorite' => $snippetDTO->is_favorite,
                'user_id' => Auth::id(),
            ];

            if ($snippet) {
                $snippet = $this->snippetRepository->update($snippet->id, $snippetData);
            } else {
                $snippet = $this->snippetRepository->create($snippetData);
            }

            // Handle tags if provided
            if (!empty($snippetDTO->tags)) {
                $this->tagRepository->syncSnippetTags($snippet->id, $snippetDTO->tags);
            }

            DB::commit();
            return $snippet;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Toggle the favorite status of a snippet.
     *
     * @param int $id
     * @return Snippet|null
     */
    public function toggleFavorite(int $id): ?Snippet
    {
        $snippet = $this->snippetRepository->find($id);

        // Check if snippet exists and belongs to current user
        if (!$snippet || $snippet->user_id !== Auth::id()) {
            return null;
        }

        $snippet = $this->snippetRepository->update($id, [
            'is_favorite' => !$snippet->is_favorite
        ]);

        return $snippet;
    }

    /**
     * Delete a snippet.
     *
     * @param int $id
     * @return bool
     */
    public function deleteSnippet(int $id): bool
    {
        $snippet = $this->snippetRepository->find($id);

        // Check if snippet exists and belongs to current user
        if (!$snippet || $snippet->user_id !== Auth::id()) {
            return false;
        }

        return $this->snippetRepository->delete($id);
    }
}
