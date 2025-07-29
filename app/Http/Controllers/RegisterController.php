<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Redirect;
use Auth;
use App\Events\LogUserRegistered;
use DB;
use Mail;
use App\Models\UserLogs;
use App\Mail\PasswordHint;
use App\Models\Option;
use Exception;

class RegisterController extends Controller
{
    //

    public function index() {

        if (Auth::check()) {
           return redirect()->route('login');
        } else {
           return view('auth/registration');
        }  

    }
    public function store(Request $request) {

        $v = Validator::make($request->all(), array(
            'firstname' => 'required',
            'lastname'  => 'required',
            'country'   => 'required',
            'phone'     => 'required',
            'email'     => 'required|unique:users',
            'company'   => 'required',
            'password' => 'required|min:6'
        ));
        //$v->passes();   // returns true;

        if( $v->passes() ) {

                $user = New User;
                 $user->firstname   = strip_tags( $request->input('firstname') );
                 $user->lastname    = strip_tags( $request->input('lastname') );
                 $user->email       = strip_tags( $request->input('email') );
                 $user->company     = strip_tags( $request->input('company') );
                 $user->country     = strip_tags( $request->input('country') );
                 if( $request->input('subscribe') ) {
                    $user->subscribe    = 1;
                 }
                 $user->phone       = strip_tags(  $request->input('phone') );
                 $user->password    = bcrypt($request->input('password'));
                 $user->password_hint = strip_tags( $request->input('password_hint') );
                 $user->status      = 0;
                 $user->save();

                 $logs = new UserLogs();   
                 $logs->type = '<span class="box-bg bg-secondary">User Registration</span>';  
                 $logs->description = '<strong>User</strong> has registered!';
                 $logs->old_value = 'Pending';
                 $logs->new_value = 'Pending';
                 $logs->action = 'update';
                 $logs->user_id = $user->id; 
                 $logs->created_by = $user->id; 
                 $logs->save();

                // listen to this event and do something
                 event(new LogUserRegistered($user));

                 $role = DB::table('roles')->where('name', 'customer')->value('id');   
                 // attach role as customer
                 $user->attachRole($role);  

                 $request->session()->flash('alert-success', 'Account has been successfully registered! Please wait for the team to approve your account');
                 return back();
        
        } 
        else 
        {

            return redirect('registration')
                        ->withErrors($v)
                        ->withInput();
        }

    }

    public function password_hint() 
    {
         return view('auth/passwords/hint')->with(['error' => '']);
    }

    public function privacy_policy()
    {
       $GDPR  = Option::where('key','GDPR' )->value('value');

        return view('auth/privacy-policy')->with(['GDPR' => $GDPR]);        
    }

    public function send_password_hint(Request $request) 
    {
        $email         = strip_tags($request->input('email'));
        $user          = DB::table('users')->where('email', $email )->get();
        $password_hint = DB::table('users')->where('email', $email )->value('password_hint');

        if(count($user) > 0) {

            if($password_hint) {
                $error = 'Please check your email for your password hint.';
                $has_error = 0;
                $name = DB::table('users')->where('email', $email )->value('firstname');

                try {
                    Mail::to( $email )->send( New PasswordHint($password_hint, $name) );
                }
                catch(\Exception $e) {}
            } else {

                $error = 'Sorry, no password hint has been set for ' . $email  . '.';
                $has_error = 1;

            }

            return view('auth/passwords/hint')->with(['error' => $error, 'has_error' => $has_error]);

        } else {
            $error = 'Email is not a registered user!';
            $has_error = 1;

            return view('auth/passwords/hint')->with(['error' => $error, 'has_error' => $has_error]);
        }
    }

}
