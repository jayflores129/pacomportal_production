<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordHint extends Mailable
{
    use Queueable, SerializesModels;

    public $hint;
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($hint, $name)
    {
        $this->hint = $hint;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
            return $this
                    ->from('support@pacom.com')
                    ->subject('[Pacom] Here is your password hint.')
                    ->view('emails/auth/send-password-hint')
                    ->with([
                        'hint' => $this->hint,
                        'name' => $this->name
                    ]);
    }
}
