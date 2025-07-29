<?php

namespace App\Listeners;

use App\Events\TaskUpdates;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Mail\TaskUpdatesMail;
use App\Models\Option;
use App\Models\User;

class TaskUpdatesNotification
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
     * @param  TaskUpdates  $event
     * @return void
     */
    public function handle(TaskUpdates $event)
    {
        
        $admin_email = Option::where('key','notification_new_task_email' )->value('value'); 

        $user = $event->user;

        try {
            Mail::to($user->email)->send(new TaskUpdatesMail($user));
            Mail::to($admin_email)->send(new TaskUpdatesMail($user));
            Mail::to( $user->submitter )->send(new TaskUpdatesMail($user));
        }
        catch (\Exception $e) {}
    }

}
