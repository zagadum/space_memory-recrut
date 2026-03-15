<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlsPaymentPlan extends Model
{
    protected $table = 'gls_payment_plans';

    protected $fillable = [
        'project_id',
        'months',
        'lessons',
        'price',
        'currency',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'months'      => 'integer',
            'lessons'     => 'integer',
            'price'       => 'decimal:2',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'sort_order'  => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(GlsProject::class, 'project_id');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = (int) ($this->months ?? 0);

        return match (true) {
            $months === 1 => '1 месяц',
            in_array($months % 10, [2, 3, 4], true) && !in_array($months % 100, [12, 13, 14], true) => $months . ' месяца',
            default => $months . ' месяцев',
        };
    }
}

