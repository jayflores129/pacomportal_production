<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\User;
use DB;
use Auth;
use Redirect;
use App\Events\ApproveUsers;
use App\Models\Role;
use Breadcrumbs;
use App\Models\Repairs;
use App\Models\UserDetail;
use App\Models\UserLogs;

class CustomerController extends Controller
{

    public $id;

    public function __construct() {


    }

    /**
     * Display a listing of customers
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $users = DB::table('users') 
                      ->join('role_user', 'users.id', '=', 'role_user.user_id')
                      ->where('role_user.role_id', '=', 4)
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('customers/index')->with([ 'users' => $users ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();

        return view('customers/create')->with('roles', $roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $v = Validator::make($request->all(), array(
            'firstname' => 'required',
            'lastname'  => 'required',
            'country'   => 'required',
            'phone'     => 'required',
            'email'     => 'required|unique:users',
            'company'   => 'required',
            'password'  => 'required|min:6|confirmed|numbers|letters',
            'role'      => 'required'
        ));
        //$v->passes();   // returns true;

        if( $v->passes() ) {

                 $user = New User;
                 $user->firstname   = $request->input('firstname');
                 $user->lastname    = $request->input('lastname');
                 $user->email       = $request->input('email');
                 $user->company     = $request->input('company');
                 $user->country     = $request->input('country');
                 $user->phone       = $request->input('phone');
                 $user->password    = bcrypt($request->input('password'));
                 $user->status      = 1;
                 $user->save();

                // listen to this event and do something
                 //event(new LogUserRegistered($user));

                 // attach role as customer
                $user->attachRole($request->input('role'));

                $request->session()->flash('alert-success', 'Account has been successfully registered!');
                return back();
        
        } 
        else 
        {

            return redirect('admin/customers/create')
                        ->withErrors($v)
                        ->withInput();
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

       $user = User::findorfail($id);
       $repairs = Repairs::where('user_id', $id)->get();

       $has_meta = DB::table('user_details')->where('user_id', $id)->count();

       if( $has_meta > 0 ) {
            $meta = UserDetail::where('user_id', $id)->get();
       } else {
            $meta = '';
       }   

       $user_logs = UserLogs::where('user_id', $id)->get();

       return view('customers/show')->with(['user' => $user, 'repairs' => $repairs, 'logs' => $user_logs, 'usermeta' => $meta ]); 

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
