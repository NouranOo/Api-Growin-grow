<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //9
    protected $fillable=array('id','status','rate',
                              'seen','notes','feedback',
                              'description','cart_id',
                              'pharmacy_id','company_id');

    public function pharmacy(){
        return $this->belongsTo('App\Models\pharmacy');
    }
  
    public function company(){
        return $this-> belongsTo('App\Models\company');
    }
    public function cart(){
        return $this->hasMany('App\Models\cartItem','order_id');
    }
}
