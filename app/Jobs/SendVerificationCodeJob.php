<?php

namespace App\Jobs;

use App\Mail\RestoreMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;

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
            Mail::to($this->email)->send(new VerificationCodeMail($this->code));

            $resetInsert['email']=$this->email;
            $resetInsert['realname']='zagadum@ukr.net';

            $objMail = new \stdClass();
            $objMail->username=$resetInsert['realname'];
            $objMail->email=$resetInsert['email'];
            $objMail->restore_url='https://'.$_SERVER['SERVER_NAME'].'/reset-password/'.$resetInsert['token'];
            $objMail->token=$resetInsert['token'];

            Mail::to($resetInsert['email'])->send(new RestoreMail($objMail));
            Log::info("Verification code sent to {$this->email}");
        } catch (\Exception $e) {
            Log::error("Failed to send verification code to {$this->email}: " . $e->getMessage());
        }
    }
}
