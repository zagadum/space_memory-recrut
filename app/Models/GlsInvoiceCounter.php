<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlsInvoiceCounter extends Model
{
    protected $table = 'gls_invoice_counters';

    public $timestamps = false;

    protected $fillable = [
        'project_id',
        'period_year',
        'period_month',
        'last_number',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'project_id'   => 'integer',
            'period_year'  => 'integer',
            'period_month' => 'integer',
            'last_number'  => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(GlsProject::class, 'project_id');
    }
}
