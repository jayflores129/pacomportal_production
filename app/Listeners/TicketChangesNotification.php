<?php

namespace App\Listeners;

use App\Events\TicketChanges;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Mail\TicketStatusMail;


class TicketChangesNotification
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
     * @param  TicketChanges  $event
     * @return void
     */
    public function handle(TicketChanges $event)
    {
        $user = $event->user;


        try {
            Mail::to($user->email)->send( new TicketStatusMail($user) );
        }
        catch (\Exception $e) {}
    }
}
