<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {    
        $expirationMinutes = 15; // O el tiempo real que estás usando para la expiración del código
        $expirationTime = now()->addMinutes($expirationMinutes)->format('H:i'); // Calcula la hora de expiración
        
        return (new MailMessage)
            ->line(__('global.two_factor.your_code_is', ['code' => $notifiable->two_factor_code]))
            ->action(__('global.two_factor.verify_here'), route('twoFactor.show'))
            ->line(__('global.two_factor.will_expire_in', ['minutes' => $expirationMinutes]))
            ->line(__('global.two_factor.ignore_this'));
    }
}