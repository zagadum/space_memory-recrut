<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMailable;

class SendVerificationCodeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $email,
        protected string $code
    ) {}

    public function handle(): void
    {
        try {
            Mail::to($this->email)->send(new VerificationCodeMailable($this->code));
            Log::info("Verification code sent to {$this->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send verification code to {$this->email}: " . $e->getMessage());
        }
    }
}
