<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "title" => $this->title,
            "slug" => $this->slug,
            "body" => $this->body,
            "cover" => asset($this->cover),
            "created_at" => $this->created_at?->format('d M Y'),
            "user" => new UserResource($this->user)
        ];
    }
}
