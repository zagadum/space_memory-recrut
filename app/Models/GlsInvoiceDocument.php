<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlsInvoiceDocument extends Model
{
    protected $table = 'gls_invoice_documents';

    protected $fillable = [
        'student_id',
        'project_id',
        'transaction_id',
        'charge_id',
        'document_type',
        'number',
        'issue_date',
        'sale_date',
        'service_date_from',
        'service_date_to',
        'title',
        'amount_net',
        'amount_gross',
        'currency',
        'ksef_status',
        'ksef_reference',
        'pdf_path',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'issue_date'        => 'date:Y-m-d',
            'sale_date'         => 'date:Y-m-d',
            'service_date_from' => 'date',
            'service_date_to'   => 'date',
            'amount_net'        => 'decimal:2',
            'amount_gross'      => 'decimal:2',
            'meta'              => 'array',
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

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(GlsPaymentTransaction::class, 'transaction_id');
    }
}
