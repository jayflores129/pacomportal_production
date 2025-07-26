<?php

namespace App\Listeners;

use App\Events\LogUserRegistered;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\PendingApproval;
use App\Mail\UserRegisteredNotification;
use App\Mail\WelcomeUser;
use Illuminate\Mail\Mailer;
use Mail;
use App\Models\User;
use App\Models\Option;

class RegistrationNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  LogUserRegistered  $event
     * @return void
     */
    public function handle(LogUserRegistered $event)
    {

        $user = $event->user;

        $admin_email = Option::where('key', 'notification_new_user_email')->value('value');

        // Send email to Admin
        Mail::to($admin_email)->send(new UserRegisteredNotification($user));

        // Send new user a welcome email 
        Mail::to($user->email)->send(new WelcomeUser($user));

        if($user->no_notification == false ) {
            //User::where('email', $admin_email)->notify(new PendingApproval);
        }
        
    }
}
