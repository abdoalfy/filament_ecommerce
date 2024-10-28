<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attribute extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded=[];

    public function variants(){
        return $this->hasMany(Variant::class);
    }


    public function attributeProducts()
    {
        return $this->hasMany(AttributeProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product');
    }

}
