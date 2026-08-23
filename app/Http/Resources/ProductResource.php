<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Scalar\Float_;

class ProductResource extends JsonResource
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
            'product_category_id' => $this->product_category_id,
            'image' => $this->image,
            'name' => $this->name,
            'price' => (float)(string) $this->price,
            'stock' => $this->stock,

            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'image' => $this->category?->image,
            ],
        ];
    }
}
