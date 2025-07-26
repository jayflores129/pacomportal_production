<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MailResetPasswordNotification extends Notification
{
    use Queueable;
    public $token;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

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


       //$link = url( "/password/reset/?token=" . $this->token );
       $link = url( "/password/reset/" . $this->token );

       $name = ( $notifiable->firstname ) ? 'Dear ' .$notifiable->firstname : 'Dear Customer';

       return ( new MailMessage )
          ->from('info@spgcontrols.com')
          ->subject( 'Reset your password' ) 
          ->markdown('emails.auth.reset-password', [
            'action' =>  $link,
            'name' => $name,
            'line' => 'You are receiving this email because a password reset request for your account.',
            'line2' => 'If you did not request a password reset, no further action is required.'
          ]);

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
