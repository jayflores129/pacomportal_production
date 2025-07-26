<?php

namespace App\Listeners;

use App\Events\NewTask;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;
use App\Models\Option;
use App\Mail\NewTaskMail;
use DB;
use App\Models\User;
use App\Notifications\NewTaskNotify;

class NewTaskNotification
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
     * @param  NewTask  $event
     * @return void
     */
    public function handle(NewTask $event)
    {
        $this->user = $event->user;

        $admin_email = Option::where('key', 'notification_new_task_email')->value('value');

        //For admin
        $this->user->myRole = 'admin';
        Mail::to($admin_email)
              ->send(new NewTaskMail($this->user));

         $email = DB::table('users')->where('id', $this->user->assigned_to)->value('email');    

        //For Assignee  
        $this->user->myRole = 'customer';    
        Mail::to($email)
              ->send(new NewTaskMail($this->user));      

        User::find($this->user->assigned_to)->notify(new NewTaskNotify($this->user->id));
    }
}
