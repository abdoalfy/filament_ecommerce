<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded=[];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function product(){
        return $this->belongsTo(Product::class);
    }
}

