<?php

namespace App\Notifications;

use App\User;
use Appp\Evento;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use SemyonChetvertnyh\ApnNotificationChannel\ApnMessage;

class MatchSuccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $with, Evento $event)
    {
        $this->user = $with->name;
        $this->evento = $event->nombre; 
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['apn'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the APN representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return ApnMessage
     */
    public function toApn($notifiable)
    {
        return ApnMessage::create()
            ->badge(1)
            ->title("¡MATCH!")
            ->body("$this->user también va a ir a $this->evento y quiere concerte");
            // ->sound()
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
