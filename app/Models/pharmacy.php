<?php

namespace App\Models;
// use Illuminate\Notifications\Notifiable; //for reset password
// use App\Notifications\LarashopPharmacyResetPassword;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Foundation\Auth\User as Authenticatable;
 /**
 * @OA\Schema()
 */
class pharmacy extends Model
{
  protected $table="pharmacies";



    //15
    protected $fillable=array('id','pharmacy_name','email','password','pharmacy_address','mobile_number',
                              'pharmacy_license','union_license',
                              'owner_name','commerical_registration',
                              'region','tax_card','available_time',
                              'branch_number','doctor_name', 'setting','verified','area_id','Token','ApiToken','logo' );

     public function companies(){
            return $this->belongsToMany('App\Models\company','rates');
    }
    public function orders(){
        return $this->hasMany('App\Models\order');
    }
    public function area(){
        return $this->belongsTo('App\Models\area');
    }
 protected $hidden=array('password','Token');
                
                             
}
