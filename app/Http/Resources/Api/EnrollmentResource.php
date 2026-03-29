<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $statusMap = [
            'pending' => 'ожидает оплаты',
            'success' => 'оплачено',
            'failed' => 'ошибка оплаты',
            'cancelled' => 'отменено',
        ];


        return [
            'id' => $this->id,
            'payment_status' => $statusMap[$this->status] ?? $this->status,
            'product' => new ProductResource($this->product),
        ];
    }
}
