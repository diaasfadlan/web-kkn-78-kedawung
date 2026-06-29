<?php

namespace App\Models;

class Member
{
    public string $id;
    public string $name;
    public string $nim;
    public string $prodi;
    public string $position;
    public string $photo_url;
    public array $social_media;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->nim = $data['nim'] ?? '';
        $this->prodi = $data['prodi'] ?? '';
        $this->position = $data['position'] ?? '';
        $this->photo_url = $data['photo_url'] ?? '';
        $this->social_media = $data['social_media'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nim' => $this->nim,
            'prodi' => $this->prodi,
            'position' => $this->position,
            'photo_url' => $this->photo_url,
            'social_media' => $this->social_media,
        ];
    }
}
