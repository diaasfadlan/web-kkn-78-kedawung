<?php

namespace App\Models;

use Carbon\Carbon;

class WorkProgram
{
    public string $id;
    public string $title;
    public string $description;
    public string $objective;
    public string $status;
    public Carbon $start_date;
    public Carbon $end_date;
    public string $thumbnail_url;
    public array $gallery;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? '';
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->objective = $data['objective'] ?? '';
        $this->status = $data['status'] ?? 'planned';
        $this->start_date = isset($data['start_date']) ? Carbon::parse($data['start_date']) : now();
        $this->end_date = isset($data['end_date']) ? Carbon::parse($data['end_date']) : now();
        $this->thumbnail_url = $data['thumbnail_url'] ?? '';
        $this->gallery = $data['gallery'] ?? [];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'objective' => $this->objective,
            'status' => $this->status,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'thumbnail_url' => $this->thumbnail_url,
            'gallery' => $this->gallery,
        ];
    }
}
