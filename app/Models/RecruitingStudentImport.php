<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitingStudentImport extends Model
{
    protected $table = 'recruiting_student_import';

    protected $fillable = [
        'email', 'name', 'surname', 'phone', 'subject',
        'source', 'campaign_id', 'token', 'status',
        'email_sent_at', 'email_opened_at', 'link_clicked_at',
        'converted_at', 'converted_student_id', 'error_message', 'meta',
    ];

    protected function casts(): array
    {
        return [
            'email_sent_at'    => 'datetime',
            'email_opened_at'  => 'datetime',
            'link_clicked_at'  => 'datetime',
            'converted_at'     => 'datetime',
            'meta'             => 'array',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(RecruitingCampaign::class, 'campaign_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConverted(): bool
    {
        return $this->status === 'converted';
    }
}
