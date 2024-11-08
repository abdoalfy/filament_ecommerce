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


    // Accessor for the main image URL
    public function getMainImageUrlAttribute()
    {
        return $this->main_image ? asset('storage/' . $this->main_image) : null;
    }

    // Accessor to retrieve all images for attributes
    public function getAttributeImagesAttribute()
    {
        return $this->attributeProductImages->map(function ($image) {
            return asset('storage/' . $image->image); // Assuming `image` column holds the path for attribute images
        });
    }

    public function InvoiceItem(){
        return $this->hasMany(InvoiceItem::class);
    }


}
