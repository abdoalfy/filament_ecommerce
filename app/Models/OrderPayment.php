<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    use HasFactory;
    protected $fillable=['order_id','invoice_id','invoice_url','user_id','price','status','isSuccess','transactionData'];
    // protected $guarded=[];
}

