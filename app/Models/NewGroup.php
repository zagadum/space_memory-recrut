<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NewGroup extends Model
{
    protected $table = 'new_groups';

    protected $fillable = [
        'name',
        'type',
        'start_date',
        'day',
        'time',
        'age_range',
        'teacher_id',
        'total_slots',
        'paid_count',
        'manager_id',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Тренер группы
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    /**
     * Менеджер группы
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'manager_id');
    }

    /**
     * Студенты группы
     */
    public function students(): HasMany
    {
        return $this->hasMany(NewGroupStudent::class, 'new_group_id');
    }
}

