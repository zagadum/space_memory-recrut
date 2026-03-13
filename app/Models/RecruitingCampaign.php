<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitingCampaign extends Model
{
    protected $table = 'recruiting_campaigns';

    protected $fillable = [
        'name', 'status', 'total_count', 'sent_count',
        'failed_count', 'clicked_count', 'converted_count',
        'email_subject', 'email_template', 'created_by',
        'started_at', 'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function imports(): HasMany
    {
        return $this->hasMany(RecruitingStudentImport::class, 'campaign_id');
    }
}
