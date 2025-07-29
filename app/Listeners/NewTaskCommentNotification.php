<?php

namespace App\Listeners;

use App\Events\NewTaskComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Mail\CommentTask;
use App\Notifications\TaskCommentNotification;
use App\Models\User;


class NewTaskCommentNotification
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
     * @param  NewTaskComment  $event
     * @return void
     */
    public function handle(NewTaskComment $event)
    {
        $user = $event->user;

        try {
            //send notification email to the user
            Mail::to($user->email)->send( New CommentTask($user) );
        
            // Add bell notification to the company
            User::find($user->id)->notify(new TaskCommentNotification($user->ticket_id));
        }
        catch (\Exception $e) {}
    }
}
