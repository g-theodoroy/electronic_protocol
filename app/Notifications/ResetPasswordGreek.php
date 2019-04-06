<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordGreek extends Notification
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
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->Subject('Ανάκτηση Password')
            ->line('Λάβατε αυτό το email γιατί μας απεστάλη αίτηση ανάκτησης password για το λογαριασμό σας.')
            ->action('Ανάκτηση Password', url('password/reset', $this->token))
            ->line('Αν δεν ζητήσατε εσείς την ανάκτηση δεν είναι αναγκάιο να προβείτε σε κάποια ενέργεια. Απλά αγνοήστε το μήνυμα.');
    }
}
