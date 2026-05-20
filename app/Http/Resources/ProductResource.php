<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ProductResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'current_price' => $this->getCurrentPrice(),
            'discount_percentage' => $this->getDiscountPercentage(),
            'stock' => $this->stock,
            'sku' => $this->sku,
            'unit' => $this->unit,
            'weight' => $this->weight,
            'images' => is_array($this->images) ? array_map(fn($img) => $img ? asset($img) : null, $this->images) : [],
            'featured_image' => $this->featured_image ? asset($this->featured_image) : null,
            'is_featured' => $this->is_featured,
            'is_active' => $this->is_active,
            'view_count' => $this->view_count,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
