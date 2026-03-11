<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestoreMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mailVars;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailVars)
    {
        $this->mailVars = $mailVars;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->view('emails.restore_password')->with( (array)$this->mailVars )->subject('Відновлення паролю!');
    }
}
