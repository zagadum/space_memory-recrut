<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlsInvoiceCounter extends Model
{
    protected $table = 'gls_invoice_counters';

    public $timestamps = false;

    protected $fillable = [
        'prefix',
        'year',
        'month',
        'last_number',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'year'        => 'integer',
            'month'       => 'integer',
            'last_number' => 'integer',
        ];
    }
}
