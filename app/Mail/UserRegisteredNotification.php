<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use App\Models\Option;

class UserRegisteredNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $newUser;
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

        $subject     = Option::where('key','new_registration_admin_subject' )->value('value');
        return $this->from('support@pacom.com')
                    ->subject($subject)
                    ->view('emails/user/admin')
                    ->with([

                        'email'     => $this->user->email, 
                        'company'   => $this->user->company, 
                        'firstname' => $this->user->firstname,
                        'lastname'  => $this->user->lastname,
                        'country'   => $this->user->country,
                        'phone'     => $this->user->phone

                    ]);
    }
}
