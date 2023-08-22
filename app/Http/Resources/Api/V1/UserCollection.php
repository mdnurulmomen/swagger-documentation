<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'current_page' => $this->currentPage(),
            'data'=> UserResource::collection($this->collection),
            "first_page_url"=> $this->url(1),
            'from'=> $this->firstItem(),
            'last_page'=> $this->lastPage(),
            "last_page_url"=> $this->url($this->lastPage()),
            'next_page_url'=> $this->nextPageUrl(),
            'path'=> route('admin.users.index'),
            'per_page'=> $this->perPage(),
            'prev_page_url'=> $this->previousPageUrl(),
            'to'=> $this->lastItem(),
            'total'=> $this->total(),
        ];
    }
}
