<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentDocument extends Model
{
    protected $table = 'payment_documents';

    protected $fillable = [
        'student_id',
        'student_payment_id',
        'doc_type',
        'fvnum',
        'amount',
        'currency',
        'issue_date',
        'pay_date',
        'reason',
        'note',
        'method',
        'iban',
        'status',
        'payload',
    ];

    protected $casts = [
        'amount' => 'float',
        'issue_date' => 'date:Y-m-d',
        'pay_date' => 'date:Y-m-d',
    ];
}

