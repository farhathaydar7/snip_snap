<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class TagRepository implements TagRepositoryInterface
{
    /**
     * Get all tags for a user.
     *
     * @param int $userId
     * @return Collection
     */
    public function getAllTags(int $userId): Collection
    {
        return Tag::where('user_id', $userId)->get();
    }

    /**
     * Find a tag by ID.
     *
     * @param int $id
     * @return Tag|null
     */
    public function find(int $id): ?Tag
    {
        return Tag::find($id);
    }

    /**
     * Find a tag by name and user ID.
     *
     * @param string $name
     * @param int $userId
     * @return Tag|null
     */
    public function findByName(string $name, int $userId): ?Tag
    {
        return Tag::where('name', $name)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Create a new tag.
     *
     * @param array $data
     * @return Tag
     */
    public function create(array $data): Tag
    {
        return Tag::create($data);
    }

    /**
     * Update an existing tag.
     *
     * @param int $id
     * @param array $data
     * @return Tag
     */
    public function update(int $id, array $data): Tag
    {
        $tag = $this->find($id);
        $tag->update($data);
        return $tag->fresh();
    }

    /**
     * Delete a tag.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $tag = $this->find($id);
        return $tag ? $tag->delete() : false;
    }

    /**
     * Sync tags for a snippet.
     *
     * @param int $snippetId
     * @param array $tagNames
     * @return void
     */
    public function syncSnippetTags(int $snippetId, array $tagNames): void
    {
        $userId = Auth::id();
        $tagIds = [];

        foreach ($tagNames as $tagName) {
            // Find or create the tag
            $tag = $this->findByName($tagName, $userId);

            if (!$tag) {
                $tag = $this->create([
                    'name' => $tagName,
                    'user_id' => $userId
                ]);
            }

            $tagIds[] = $tag->id;
        }

        // Sync the tags with the snippet
        $snippet = \App\Models\Snippet::findOrFail($snippetId);
        $snippet->tags()->sync($tagIds);
    }
}
