<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
            [
                'data' => $this->collection,
                'pagination' => [
                    'total' => $this->total(),
                    'current' => $this->currentPage(),
                    'per_page' => $this->perPage(),
                ],
            ];
    }
    public function toResponse($request)
    {
        return response()->json($this->toArray($request));
    }
}
