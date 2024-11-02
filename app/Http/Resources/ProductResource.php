<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'mainImage' => $this->main_image,
            'basePrice' => $this->base_price,
            'points' => $this->points,
            'status' => $this->status,
            'category'=>$this->whenLoaded('category'),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
         //'attributes' => AttributeResource::collection($this->whenLoaded('attributes')),
            'attributeProducts' => AttributeProductResource::collection($this->whenLoaded('attributeProducts')),
        ];
    }
}