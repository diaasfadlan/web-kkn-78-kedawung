<?php

namespace App\Models;

class GroupProfile
{
    public string $name;
    public string $location;
    public string $period;
    public string $university;
    public string $description;
    public string $logo_url;
    public string $photo_url;
    public array $social_media;

    public function __construct(array $data = [])
    {
        $this->name = $data['name'] ?? '';
        $this->location = $data['location'] ?? '';
        $this->period = $data['period'] ?? '';
        $this->university = $data['university'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->logo_url = $data['logo_url'] ?? '';
        $this->photo_url = $data['photo_url'] ?? '';
        $this->social_media = $data['social_media'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'location' => $this->location,
            'period' => $this->period,
            'university' => $this->university,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'photo_url' => $this->photo_url,
            'social_media' => $this->social_media,
        ];
    }
}
