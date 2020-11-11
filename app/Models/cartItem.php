<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cartItem extends Model
{
   protected $table='carts';
    //
    protected $fillable=array('id','name_ar_eg','name_en_us',
                              'price','tax_card','qty',
                              'discount_percentage','discount_buy',
                              'discount_get','company_id','area_id','order_id');


     public function order(){
    return $this->belongsTo('App\Models\order','order_id'); 
    }

    }
   
