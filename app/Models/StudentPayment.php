<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentPayment extends Model
{
    protected $table = 'student_payment';

    protected $fillable = [
        'student_id',
        'date_pay',
        'date_finish',
        'sum_aboniment',
        'aboniment_id',
        'type_pay',
        'discount',
        'comment',
        'enabled',

    ];


    protected $dates = [
        'date_pay',
        'date_finish',
        'created_at',
        'updated_at',

    ];

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/student-payments/'.$this->getKey());
    }

    /**
     * @return HasMany
     */
    public function Documents()
    {
        return $this->hasMany(PaymentDocument::class, 'student_payment_id', 'id');
    }
}
