<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class product_order extends Model
{
    //
    protected $fillable=array('id','product_id','order_id');
}
