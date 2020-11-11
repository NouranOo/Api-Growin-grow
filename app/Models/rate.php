<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class rate extends Model
{
    //
    protected $fillable=array('id','rate','comment',
                              'company_id','pharmacy_id');
}
