<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\RecruitingCampaign;
use App\Models\RecruitingStudentImport;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class RecruitingInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecruitingStudentImport $import,
        public RecruitingCampaign $campaign,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('recruiting.email.subject', ['subject' => $this->import->subject ?? 'Space Memory']),
        );
    }

    public function content(): Content
    {
        $registerUrl = url('/register/invite/' . $this->import->token);

        return new Content(
            view: 'emails.recruiting-invite',
            with: [
                'name'        => trim(($this->import->name ?? '') . ' ' . ($this->import->surname ?? '')),
                'registerUrl' => $registerUrl,
                'subject'     => $this->import->subject,
            ],
        );
    }
}
