<?php

declare(strict_types=1);

namespace App\Http\Resources\Recruiting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecruitingCampaignResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'status'         => $this->status,
            'emailSubject'   => $this->email_subject,
            'emailTemplate'  => $this->email_template,
            'totalCount'     => (int) $this->total_count,
            'sentCount'      => (int) $this->sent_count,
            'failedCount'    => (int) $this->failed_count,
            'clickedCount'   => (int) $this->clicked_count,
            'convertedCount' => (int) $this->converted_count,
            'createdBy'      => $this->created_by,
            'startedAt'      => $this->started_at?->toIso8601String(),
            'completedAt'    => $this->completed_at?->toIso8601String(),
            'createdAt'      => $this->created_at?->toIso8601String(),
            'updatedAt'      => $this->updated_at?->toIso8601String(),
        ];
    }
}
