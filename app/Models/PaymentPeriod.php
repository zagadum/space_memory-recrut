<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPeriod extends Model
{
    protected $table = 'payment_period';

    protected $fillable = [
        'name',
        'discount',
        'term',

    ];


    protected $dates = [
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/payment-periods/'.$this->getKey());
    }

    public function Aboniment(){
        return $this->hasOne(PaymentPeriod::class, 'aboniment_id', 'id');
    }
}
