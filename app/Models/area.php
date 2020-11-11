<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    //
    protected $fillable=array('id','name','governorate_id');
    
    public function products(){
        return $this->belongsToMany('App\Models\product','product_areas');
    }
    public function pharmacy(){
        return $this->hasMany('App\Models\pharmacy');
    }
    public function governorate(){
        return $this->belongsTo('App\Models\Governorate');
    }
}
