<?php

namespace App\DTO;

class SnippetDTO
{
    public ?int $id;
    public string $title;
    public ?string $description;
    public string $code;
    public string $language;
    public bool $is_favorite;
    public ?array $tags;

    /**
     * SnippetDTO constructor.
     *
     * @param int|null $id
     * @param string $title
     * @param string|null $description
     * @param string $code
     * @param string $language
     * @param bool $is_favorite
     * @param array|null $tags
     */
    public function __construct(
        ?int $id,
        string $title,
        ?string $description,
        string $code,
        string $language,
        bool $is_favorite = false,
        ?array $tags = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->code = $code;
        $this->language = $language;
        $this->is_favorite = $is_favorite;
        $this->tags = $tags;
    }

    /**
     * Create a SnippetDTO from request data.
     *
     * @param array $requestData
     * @param int|null $id
     * @return self
     */
    public static function fromRequest(array $requestData, ?int $id = null): self
    {
        return new self(
            $id,
            $requestData['title'] ?? '',
            $requestData['description'] ?? null,
            $requestData['code'] ?? '',
            $requestData['language'] ?? '',
            $requestData['is_favorite'] ?? false,
            $requestData['tags'] ?? null
        );
    }
}
