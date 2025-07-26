<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\Option;
use DB;
use Auth;
use App\Models\User;

class SettingController extends Controller
{
  
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $user_notification   = Option::where('key','notification_new_user_email' )->value('value');  
        $repair_notification = Option::where('key','notification_new_repair_email' )->value('value');    
        $task_notification   = Option::where('key','notification_new_task_email' )->value('value');
        $rma_edit_permission = Option::where('key','rma_editor_permission_email' )->value('value');

        $default_assignee    = Option::where('key','default_assignee_for_new_task' )->value('value');
        $session             = Option::where('key','session_allowed_time' )->value('value');

        $spg_users           =  DB::table('users')
                                    // ->join( 'role_user', 'users.id', '=', 'role_user.user_id' )
                                    // ->where( 'role_user.role_id', '=', '5' )
                                    // ->orWhere( 'role_user.role_id', '=', '1' )
                                    ->get();
        $GDPR                = Option::where('key','GDPR' )->value('value');

        return view('settings/index')->with([
            'user_email'           => $user_notification, 
            'repair_email'         => $repair_notification, 
            'task_email'           => $task_notification,
            'default_assignee'     => $default_assignee,
            'spg_users'            => $spg_users,
            'GDPR'                 => $GDPR, 
            'rma_editor'           => $rma_edit_permission,
            'session_allowed_time' => $session ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $this->validate($request, [
                'new_user_notification' => 'required',
                'new_repair_notification' => 'required',
                'new_task_notification' => 'required',
                'default_assignee' => 'required'
        ]);

        $user_notification   = Option::where('key','notification_new_user_email' )->value('value');  
        $repair_notification = Option::where('key','notification_new_repair_email' )->value('value');  
        $task_notification   = Option::where('key','notification_new_task_email' )->value('value'); 
        $default_assignee    = Option::where('key','default_assignee_for_new_task' )->value('value');
        $rma_editor          = Option::where('key','rma_editor_permission_email' )->value('value');
        $session             = Option::where('key','session_allowed_time' )->value('value');
        $GDPR                = Option::where('key','GDPR' )->value('value');


        if( isset($rma_editor) && $request->input('rma_editor') != '' ) {
            DB::table('options')
                ->where('key','rma_editor_permission_email' )
                ->update(['value' => $request->input('rma_editor')  ]);
        } 
        else if( $request->input('rma_editor') ) {  
            $option = new Option();
            $option->key = 'rma_editor_permission_email';
            $option->value =  $request->input('rma_editor');
            $option->save();
        }

        if( isset($GDPR) && $request->input('GDPR') != '' ) {
            DB::table('options')
                ->where('key','GDPR' )
                ->update(['value' => $request->input('GDPR')  ]);
        } 
        else if( $request->input('GDPR') ) {  
            $option = new Option();
            $option->key = 'GDPR';
            $option->value =  $request->input('GDPR');
            $option->save();
        }


        if(isset($default_assignee) ) {
            DB::table('options')
                ->where('key','default_assignee_for_new_task' )
                ->update(['value' => strip_tags( $request->input('default_assignee') ) ]);
        } else {  
            $option = new Option();
            $option->key = 'default_assignee_for_new_task';
            $option->value = strip_tags(  $request->input('default_assignee') );
            $option->save();
        }

        if(isset($user_notification) ) {
            DB::table('options')
                ->where('key','notification_new_user_email' )
                ->update(['value' => $request->input('new_user_notification')]);
        } else {  
            $option = new Option();
            $option->key = 'notification_new_user_email';
            $option->value = $request->input('new_user_notification');
            $option->save();
        }

        if( isset( $repair_notification)) {
            DB::table('options')
                    ->where('key','notification_new_repair_email' )
                    ->update(['value' => $request->input('new_repair_notification')]); 
        } else { 
            $option = new Option();
            $option->key = 'notification_new_repair_email';
            $option->value = $request->input('new_repair_notification');
            $option->save();
        }

        if( isset( $task_notification)) {
            DB::table('options')
                    ->where('key','notification_new_task_email' )
                    ->update(['value' => $request->input('new_task_notification')]); 
        } else { 
            $option = new Option();
            $option->key = 'notification_new_task_email';
            $option->value = $request->input('new_task_notification');
            $option->save();
        }

        $request->session()->flash('alert-success', 'General Setting has been updated!');
        return back();
    }

    /**
     * Display Email Setting
     * @return view
     */
    public function settingEmail()
    {

        return view('settings/email');
    }


    /**
     * Display API FORM settings
     * @return view
     */
    public function setting_api()
    {
        $api_token = Auth::user()->api_token;

        return view('settings/api')->with('api_token', $api_token);
    }


    /**
     * Process API settings
     * @return view
     */
    public function update_api()
    {
        $id = Auth::user()->id;

        $api_token   = str_random(60);
        $user        = DB::table('users')->where('id',  $id )->update(['api_token' => $api_token]);

        if($user) {
            return back()->with('success', 'API Token successfully added!');
        }

    }


    /**
     * Display reset passwor form
     * @return view
     */
    public function change_password()
    {
        $user = Auth::user();

        return view('settings/password')->with( 'user', $user );
        
    }


    /**
     * Process new password from the form 
     * @return view
     */
    public function update_password(Request $request)
    {
        $userID = Auth::user()->id;

        $v = Validator::make($request->all(), array(
            'password'  => 'required|min:6',
        ));
    
        if( $v->passes() ) {
            $password         = bcrypt($request->input('password'));
            $password_hint    = strip_tags( $request->input('password_hint') );
            $user = DB::table('users')->where('id',  $userID )->update(['password' => $password, 'password_hint' => $password_hint ]);
             

            $request->session()->flash('alert-success', 'Password has been successfully updated!');
            return back();
        } 
        else 
        {
            return redirect('admin/password-change')
                        ->withErrors($v)
                        ->withInput();
        }
    }


}
