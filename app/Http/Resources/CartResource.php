<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $product = $this->attributeProduct->product;
        $attribute = $this->attributeProduct->attribute;
        $variant = $this->attributeProduct->variant;

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'product' => [
                'name' => $product ? $product->name : null,
                'main_image' => $product ? $product->main_image_url : null,
            ],
            'attribute' => $attribute ? $attribute->name : null,
            'variant' => $variant ? $variant->name : null,
        ];
    }
}
