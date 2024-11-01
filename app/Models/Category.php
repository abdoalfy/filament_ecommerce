<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded=[];

    public function children(){
        return $this->hasMany(Category::class);
    }

    public function parent(){
        return $this->belongsTo(Category::class);
    }
    
}
