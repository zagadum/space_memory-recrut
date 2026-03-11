<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use MemoryOlympiad\Models\Olympiad\MOlympiad;

class OlympiadEnding extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $participant;
    public   $olympiad;
    public   $payload;

    /**
     * @param mixed $participant
     * @param MOlympiad $olympiad
     * @param array $payload
     */
    public function __construct($participant, MOlympiad $olympiad, array $payload = [])
    {
        $this->participant = $participant;
        $this->olympiad = $olympiad;
        $this->payload = $payload;
    }

    public function build()
    {
        $subject = 'Окончание олимпиады: ' . ($this->olympiad->title ?? 'Олимпиада');
        $lang=config('app.locale', 'uk');
        return $this->subject($subject)
                    ->view('emails.olympiad.ending_'.$lang)
                    ->with([
                        'participant' => $this->participant,
                        'olympiad' => $this->olympiad,
                        'payload' => $this->payload,
                    ]);
    }
}
