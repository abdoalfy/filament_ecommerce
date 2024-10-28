<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeProductImage extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'attribute_product_images';

    protected $fillable = [
        'attribute_product_id',
        'image',
    ];

    public function attributeProduct()
    {
        return $this->belongsTo(AttributeProduct::class);
    }

    public function product()
    {
        return $this->attributeProduct->product(); // Assuming the attributeProduct relationship is defined
    }
}
