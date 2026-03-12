<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecrutingStudentHistory extends Model
{
    protected $table = 'recruting_student_history';

    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'event',
        'detail',
        'changed_by',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
