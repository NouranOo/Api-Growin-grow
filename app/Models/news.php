<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class news extends Model
{
    //
    protected $fillable=array('id','label_ar','description_ar','label_en','description_en');
}
