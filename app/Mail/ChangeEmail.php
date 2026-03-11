<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChangeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $newEmail;

    /**
     * Create a new message instance.
     *
     * @param $newEmail
     */
    public function __construct($newEmail)
    {
        $this->newEmail = $newEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if (isset($this->newEmail->password)&& isset( $this->newEmail->uuid)&&isset($this->newEmail->link)) {
            return $this
                ->view('emails.new_password')
                ->with([
                    'newEmail' => $this->newEmail->password,
                    'uuid' => $this->newEmail->uuid,
                    'link' => $this->newEmail->link,
                    'name'=>$this->newEmail->name,

                ]);
        }
        else if(isset($this->newEmail->password)&& !isset( $this->newEmail->uuid)&& !isset($this->newEmail->link)){
            return $this
                ->view('emails.new_password')
                ->with([
                    'newEmail' => $this->newEmail->password,
                    'name'=>$this->newEmail->name,
                ]);
        }
        else if (!isset($this->newEmail->password)&& !isset( $this->newEmail->uuid)&& !isset($this->newEmail->link)){
            return $this
                ->view('emails.new_email')
                ->with([
                    'newEmail' => $this->newEmail->email,
                    'name'=>$this->newEmail->name,
                ]);
        }
        else if (!isset($this->newEmail->password)&& isset( $this->newEmail->uuid)&& isset($this->newEmail->link)){
            return $this
                ->view('emails.new_email')
                ->with([
                    'newEmail' => $this->newEmail->email,
                    'uuid' => $this->newEmail->uuid,
                    'link' => $this->newEmail->link,
                    'name'=>$this->newEmail->name,
                ]);
        }
    }
}
