<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Option;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject     = Option::where('key','new_registration_customer_subject' )->value('value');
        return $this
                    ->from('support@pacom.com')
                    ->subject($subject)
                    ->view('emails/user/customer')
                    ->with([
                        'firstname' => $this->user->firstname,
                    ]);

    }
}
