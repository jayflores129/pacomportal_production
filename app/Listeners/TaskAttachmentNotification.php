<?php

namespace App\Listeners;

use App\Events\TaskAttachment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Mail\TaskAttachmentMail;

class TaskAttachmentNotification
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
     * @param  TaskAttachment  $event
     * @return void
     */
    public function handle(TaskAttachment $event)
    {
        $user = $event->user;

        try {
            Mail::to($user->email)->send( New TaskAttachmentMail($user) );
        }
        catch (\Exception $e) {}
    }
}
