<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBonus extends Model
{
    protected $table = 'student_bonus';

    protected $fillable = [
        'id',
        'student_id',
        'date',
        'alias',
        'is_notife'
    ];


    protected $dates = [

    ];
    public $timestamps = false;

    protected $appends = ['resource_url'];

    /* ************************ ACCESSOR ************************* */

    public function getResourceUrlAttribute()
    {
        return url('/admin/student-bonus/'.$this->getKey());
    }
}
