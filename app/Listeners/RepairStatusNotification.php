<?php

namespace App\Listeners;

use App\Events\RepairStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Mail;
use App\Mail\RepairStatusMail;

class RepairStatusNotification
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
     * @param  RepairChanges  $event
     * @return void
     */
    public function handle(RepairStatus $event)
    {
        $user = $event->user;

        Mail::to($user->email)->send( new RepairStatusMail($user) );
           
    }
}
