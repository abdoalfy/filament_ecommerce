<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Copoun extends Model
{
    use HasFactory;
    protected $fillable=['copoun','start_date','ena_date','limit','type','status','value'];
}
