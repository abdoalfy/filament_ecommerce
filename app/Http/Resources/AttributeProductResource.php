<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'extraPrice' => $this->extra_price,
            'quantity' => $this->quantity,
            'attribute' => new AttributeResource($this->whenLoaded('attribute')),  // Load only the relevant attribute
            'variant' => new VariantResource($this->whenLoaded('variant')),        // Load only the relevant variant
            'images' => AttributeProductImageResource::collection($this->whenLoaded('images')), // Only relevant images
        ];
    }
}
