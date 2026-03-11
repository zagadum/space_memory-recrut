<?php


namespace App\AdminModule\AdminAuth\Activation\Notifications;;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ActivationNotification extends Notification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable): MailMessage
    {
        //TODO change to template?
        return (new MailMessage)
            ->line(trans('activations.email.line'))
            ->action(trans('activations.email.action'), route('admin/activation/activate', $this->token))
            ->line(trans('activations.email.notRequested'));
    }
}
