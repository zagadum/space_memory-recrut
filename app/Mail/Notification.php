<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    public  $data=[];
    protected  $subjectText='';

    public function __construct(array $data, string $subject = 'Уведомление')
    {
        $this->data = $data;
        $this->subjectText = $subject;
    }

    public function build()
    {


        return $this->subject($this->subjectText)
            ->view('emails.notification')
            ->with($this->data);
    }
}
