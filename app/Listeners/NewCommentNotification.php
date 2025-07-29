<?php

namespace App\Listeners;

use App\Events\NewComment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\CommentNotification;
use App\Notifications\NewCommentTicket;
use Mail;
use App\Models\User;
use DB;

class NewCommentNotification
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
     * @param  NewComment  $event
     * @return void
     */
    public function handle(NewComment $event)
    {
        $user = $event->user;

        try {
            if( $user->not_assign == false ) {

                //Send assignee a notification email
                Mail::to($user->email)->send( New CommentNotification($user) );

                //Notify assignee
                User::find($user->id)->notify(new NewCommentTicket($user->ticket_id));

            }
            else {

                // Send email to company
                Mail::to( $user->email )->send( New CommentNotification($user) );
            }
        }
        catch (\Exception $e) {}

        


    }
}
