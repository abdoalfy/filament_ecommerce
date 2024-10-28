<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasTranslations;

    public $translatable = ['name','description'];

    protected $guarded=[];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function images(){
        return $this->hasMany(ProductImage::class);
    }

    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product');
    }

    public function variants()
    {
        return $this->belongsToMany(Variant::class, 'attribute_product');
    }


    public function attributeProductsomImages(){
        return $this->belongsTo(AttributeProductImage::class);
    }

    public function attributeProductImages()
    {
        return $this->hasManyThrough(AttributeProductImage::class, AttributeProduct::class, 'product_id', 'attribute_product_id');
    }


    public function InvoiceItem(){
        return $this->hasMany(InvoiceItem::class);
    }
}
