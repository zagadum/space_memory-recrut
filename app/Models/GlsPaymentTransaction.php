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
        'provider',
        'direction',
        'amount',
        'currency',
        'status',
        'provider_transaction_id',
        'provider_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'provider_payload' => 'array',
            'paid_at'          => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(GlsProject::class, 'project_id');
    }
}
