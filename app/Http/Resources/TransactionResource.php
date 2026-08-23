<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'code' => $this->code,
            'customer_id' => $this->customer_id,
            'subtotal' => $this->subtotal,
            'tax' => $this->tax,
            'total' => $this->total,

            'customer' => [
                'id' => $this->customer?->id,
                'name' => $this->customer?->name,
                'phone' => $this->customer?->phone,
            ],

            'items' => TransactionItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
