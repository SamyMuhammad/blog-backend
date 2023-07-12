<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ArticleListResource extends ArticleDetailsResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $mapping = parent::toArray($request);
        $mapping['body'] = Str::limit($this->body, 200);
        return $mapping;
    }
}
