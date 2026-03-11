<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewGroupStudent extends Model
{
    protected $table = 'new_group_students';

    protected $fillable = [
        'new_group_id',
        'student_id',
        'contract_status',
        'payment_amount',
        'payment_status',
        'joined_at',
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'joined_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Группа
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(NewGroup::class, 'new_group_id');
    }

    /**
     * Студент
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

