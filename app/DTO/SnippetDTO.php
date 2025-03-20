<?php

namespace App\DTO;

class SnippetDTO
{
    public ?int $id;
    public int $user_id;
    public string $title;
    public ?string $description;
    public string $code;
    public string $language;
    public bool $is_favorite;
    public array $tags;

    /**
     * Create a new DTO instance from request data
     *
     * @param array $data
     * @param int|null $id
     * @return self
     */
    public static function fromRequest(array $data, ?int $id = null): self
    {
        $dto = new self();
        $dto->id = $id;
        $dto->user_id = auth()->id();
        $dto->title = $data['title'];
        $dto->description = $data['description'] ?? null;
        $dto->code = $data['code'];
        $dto->language = $data['language'];
        $dto->is_favorite = $data['is_favorite'] ?? false;
        $dto->tags = $data['tags'] ?? [];
        
        return $dto;
    }

    /**
     * Convert DTO to array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'description' => $this->description,
            'code' => $this->code,
            'language' => $this->language,
            'is_favorite' => $this->is_favorite,
            'tags' => $this->tags,
        ];
    }
} 