<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'attribute_product_id', 'quantity'];

    public function user(){
        return $this->belongsTo(User::class);
    }
    
    public function attributeProduct()
    {
        return $this->belongsTo(AttributeProduct::class, 'attribute_product_id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, AttributeProduct::class, 'id', 'id', 'attribute_product_id', 'product_id');
    }


}
