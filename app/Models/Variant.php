<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Variant extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $guarded=[];

    public function attribute(){
        return $this->belongsTo(Attribute::class);
    }

    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product');
    }

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_product');
    }

}
