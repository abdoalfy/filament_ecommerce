<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $locale = app()->getLocale();
        $name = json_decode($this->name, true);
        $description = json_decode($this->description, true);

        return [
            'productId' => $this->id,
            'name' => $name[$locale] ?? $name['en'],
            'description' => $description[$locale] ?? $description['en'],
            'mainImage' => $this->main_image_url,
            'basePrice' => $this->base_price,
            'categoryId' => $this->category_id,
            'points' => $this->points,
            'status' => $this->status,
            'quantity' => $this->whenPivotLoaded('carts', fn() => $this->pivot->quantity),
            'attributeImages' => $this->attribute_images,
        ];
    }
}
