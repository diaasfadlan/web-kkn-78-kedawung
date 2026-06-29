<?php

namespace App\Models;

use Carbon\Carbon;

class Article
{
    public string $id;
    public string $title;
    public string $slug;
    public string $content;
    public string $thumbnail_url;
    public string $category;
    public string $author;
    public Carbon $published_at;
    public array $gallery;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->slug = $data['slug'] ?? '';
        $this->content = $data['content'] ?? '';
        $this->thumbnail_url = $data['thumbnail_url'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->author = $data['author'] ?? '';
        $this->published_at = isset($data['published_at']) ? Carbon::parse($data['published_at']) : now();
        $this->gallery = $data['gallery'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'thumbnail_url' => $this->thumbnail_url,
            'category' => $this->category,
            'author' => $this->author,
            'published_at' => $this->published_at,
            'gallery' => $this->gallery,
        ];
    }
}
