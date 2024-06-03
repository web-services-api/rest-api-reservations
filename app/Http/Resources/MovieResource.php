<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\CategoryMovie;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->description,
            'release_date' => $this->release_date,
            'rating' => $this->rating,
            'media' => $this->media_id ?? null,
            'categories' => CategoryResource::collection($this->categories),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
