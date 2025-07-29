<?php

namespace App\Listeners;

use App\Events\ApproveUsers;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ApproveUser;
use Mail;

class ApproveUserNotification
{
    protected $user;
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
     * @param  ApproveUsers  $event
     * @return void
     */
    public function handle(ApproveUsers $event)
    {
        
        $user = $event->user;

        try {
            Mail::to($user->email)->send(new ApproveUser($user));
        }
        catch (\Exception $e) {}

    }
}
