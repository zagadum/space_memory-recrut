<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BugtrackerMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;  // Данные для письма

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject('Bugtracker')
            ->view('emails.bugtracker')  // Путь к email-шаблону
            ->with(['data' => $this->data]);
    }
}
