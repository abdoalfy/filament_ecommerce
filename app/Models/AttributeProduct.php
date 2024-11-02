<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttributeProduct extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'attribute_product';

    protected $fillable = [
        'product_id',
        'attribute_id',
        'variant_id',
        'extra_price',
        'quantity',
    ];

    // public function product()
    // {
    //     return $this->belongsTo(Product::class);
    // }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function images()
    {
        return $this->hasMany(AttributeProductImage::class);
    }


    public function attributeProductImages()
    {
        return $this->hasMany(AttributeProductImage::class);
    }

}
