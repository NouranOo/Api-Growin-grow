<?php

namespace App\Models;

// use App\Notifications\LarashopCompanyResetPassword; // for reset password
//Notification for Seller

// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Notifications\Notifiable;

class company extends Model
{
     

    //
    protected $fillable = array('id', 'name', 'email', 'password', 'news_content',
        'offer_content', 'offer_title', 'tax_percentage',
        'order_limit', 'currency','csvfile',
        'round', 'discount_type','warningLimit',
        'verified', 'pay_method_id', 'logo', 'address');

    public function products()
    {

        return $this->belongsToMany('App\Models\product', 'company_products');
    }
    public function quota()
    {
        return $this->belongsToMany('App\Models\product', 'company_products')->where('quota_order_limit', '!=', 0);

    }

    public function pay_method()
    {
        return $this->belongsTo('App\Models\pay_method');
    }
    public function pharmacies()
    {
        return $this->belongsToMany('App\Models\pharmacy', 'rates');
    }
    public function orders()
    {
        return $this->hasMany('App\Models\order');
    }

  

}
