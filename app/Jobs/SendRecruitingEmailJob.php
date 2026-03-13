<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\RecruitingInviteMail;
use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class SendRecruitingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $backoff = 120;

    public function __construct(
        public RecruitingStudentImport $import,
    ) {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $campaign = $this->import->campaign;

        Mail::to($this->import->email)->send(
            new RecruitingInviteMail($this->import, $campaign)
        );

        $this->import->update([
            'status'        => 'sent',
            'email_sent_at' => now(),
        ]);

        // Increment campaign counter
        RecruitingCampaign::query()
            ->where('id', $campaign->id)
            ->increment('sent_count');

        Log::channel('recruiting')->info('Email sent', [
            'import_id' => $this->import->id,
            'email'     => $this->import->email,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        $this->import->update([
            'status'        => 'failed',
            'error_message' => $exception->getMessage(),
        ]);

        $campaign = $this->import->campaign;
        RecruitingCampaign::query()
            ->where('id', $campaign->id)
            ->increment('failed_count');

        Log::channel('recruiting')->error('Email FAILED', [
            'import_id' => $this->import->id,
            'email'     => $this->import->email,
            'error'     => $exception->getMessage(),
        ]);
    }
}
