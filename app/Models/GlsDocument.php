<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlsDocument extends Model
{
    protected $table = 'gls_documents';

    protected $fillable = [
        'student_id',
        'project_id',
        'doc_no',
        'title',
        'doc_status',
        'pdf_path',
        'sign_date',
    ];

    protected function casts(): array
    {
        return [
            'sign_date' => 'datetime',
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
}


