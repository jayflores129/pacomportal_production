<?php

namespace App\Listeners;

use App\LoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use DB;
use Carbon;
use App\Models\UserLogs;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //

        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        $has_login = DB::table('login_history')->where('company', $user->id);

        if( $has_login )
        {
                $current_count = DB::table('login_history')->where('company', $user->id)->value('total');

                if( !empty($current_count) ) {

                    $current_count = $current_count + 1;
                    $current_time  = Carbon\Carbon::now();

                    DB::table('login_history')
                            ->where('company', $user->id)
                            ->update(['total' => $current_count, 'updated_at' => $current_time ]);
                 
                }  else {
                    DB::table('login_history')
                            ->where('company', $user->id)
                            ->update(['total' => 1]);
                }   

        } 
        else 
        {
                $log = new LoginLog();
                $log->company = $user->id;
                $log->company = 1;
                $log->save();
        }


         $activty = new UserLogs();
         $activty->type = '<span class="box-bg bg-primary">User Login</span>'; 
         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has log in the website on <strong>' . date(' d-m-Y H:i:s', strtotime(Carbon\Carbon::now())) . '</strong>';
         $activty->old_value = '';
         $activty->new_value = '';
         $activty->action = 'user_activity';
         $activty->user_id = $user->id; 
         $activty->created_by = $user->id; 
         $activty->save();



    }
}
