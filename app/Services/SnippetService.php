<?php

namespace App\Services;

use App\DTO\SnippetDTO;
use App\Models\Snippet;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SnippetService
{
    protected SnippetRepositoryInterface $snippetRepository;
    protected TagRepositoryInterface $tagRepository;

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
     * Get all snippets with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllSnippets(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->snippetRepository->getAllWithFilters($filters, $perPage);
    }

    /**
     * Get a snippet by ID
     *
     * @param int $id
     * @return Snippet|null
     */
    public function getSnippet(int $id): ?Snippet
    {
        return $this->snippetRepository->findById($id);
    }

    /**
     * Create or update a snippet
     *
     * @param SnippetDTO $snippetDTO
     * @return Snippet
     */
    public function createOrUpdateSnippet(SnippetDTO $snippetDTO): Snippet
    {
        // Create or update the snippet
        $snippet = $this->snippetRepository->createOrUpdate(
            [
                'title' => $snippetDTO->title,
                'description' => $snippetDTO->description,
                'code' => $snippetDTO->code,
                'language' => $snippetDTO->language,
                'is_favorite' => $snippetDTO->is_favorite,
            ],
            $snippetDTO->id
        );

        // Handle tags
        if (!empty($snippetDTO->tags)) {
            $tagIds = [];
            
            foreach ($snippetDTO->tags as $tagName) {
                $tag = $this->tagRepository->findOrCreateByName($tagName);
                $tagIds[] = $tag->id;
            }
            
            $snippet->tags()->sync($tagIds);
        }

        return $snippet->load('tags');
    }

    /**
     * Delete a snippet
     *
     * @param int $id
     * @return bool
     */
    public function deleteSnippet(int $id): bool
    {
        return $this->snippetRepository->delete($id);
    }

    /**
     * Toggle favorite status of a snippet
     *
     * @param int $id
     * @return Snippet|null
     */
    public function toggleFavorite(int $id): ?Snippet
    {
        return $this->snippetRepository->toggleFavorite($id);
    }
} 