<?php

namespace App\Listeners;

use App\Events\DisapproveUsers;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\DisapproveUser;
use Mail;

class DisapproveUserNotification
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
     * @param  DisapproveUsers  $event
     * @return void
     */
    public function handle(DisapproveUsers $event)
    {
        $user = $event->user;

        Mail::to($user->email)->send(new DisapproveUser($user));
    }
}
