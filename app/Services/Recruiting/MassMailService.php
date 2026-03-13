<?php

declare(strict_types=1);

namespace App\Services\Recruiting;

use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use App\Jobs\SendRecruitingEmailJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

final class MassMailService
{
    /**
     * Start a campaign: dispatch email jobs for all pending imports.
     */
    public function startCampaign(RecruitingCampaign $campaign): void
    {
        $campaign->update([
            'status'     => 'sending',
            'started_at' => now(),
        ]);

        $pending = $campaign->imports()
            ->where('status', 'pending')
            ->get();

        foreach ($pending as $import) {
            SendRecruitingEmailJob::dispatch($import);
        }

        Log::channel('recruiting')->info('Campaign started', [
            'campaign_id' => $campaign->id,
            'pending'     => $pending->count(),
        ]);
    }

    /**
     * Dry run: validate emails, check duplicates, return stats.
     * NO emails sent. For auto-testing requirement.
     */
    public function dryRun(RecruitingCampaign $campaign): array
    {
        $imports = $campaign->imports()->get();

        $stats = [
            'total'            => $imports->count(),
            'valid_emails'     => 0,
            'invalid_emails'   => 0,
            'duplicate_in_students' => 0,
            'ready_to_send'    => 0,
            'invalid_list'     => [],
            'duplicate_list'   => [],
        ];

        foreach ($imports as $import) {
            if (!filter_var($import->email, FILTER_VALIDATE_EMAIL)) {
                $stats['invalid_emails']++;
                $stats['invalid_list'][] = $import->email;
                continue;
            }

            $stats['valid_emails']++;

            $existsInStudents = DB::table('recruting_student')
                ->where('email', $import->email)
                ->exists();

            if ($existsInStudents) {
                $stats['duplicate_in_students']++;
                $stats['duplicate_list'][] = $import->email;
                continue;
            }

            $stats['ready_to_send']++;
        }

        return $stats;
    }
}
