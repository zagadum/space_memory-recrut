<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlsPaymentTransaction extends Model
{
    protected $table = 'gls_payment_transactions';

    protected $fillable = [
        'student_id',
        'project_id',
        'payment_plan_id',
        'provider',
        'direction',
        'amount',
        'currency',
        'months',
        'lessons',
        'title',
        'status',
        'provider_transaction_id',
        'provider_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'months'           => 'integer',
            'lessons'          => 'integer',
            'provider_payload' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(RecrutingStudent::class, 'student_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(GlsProject::class, 'project_id');
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(GlsPaymentPlan::class, 'payment_plan_id');
    }

    public function getPeriodLabelAttribute(): string
    {
        $months = (int) ($this->months ?? 0);

        if ($months <= 0) {
            return $this->title ?? 'Оплата обучения';
        }

        return match (true) {
            $months === 1 => '1 месяц',
            in_array($months % 10, [2, 3, 4], true) && !in_array($months % 100, [12, 13, 14], true) => $months . ' месяца',
            default => $months . ' месяцев',
        };
    }
}
