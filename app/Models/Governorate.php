<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    //
    protected $fillable=array('id','name');

    public function areas(){
        return $this->hasMany('App\Models\area');
    }
}
