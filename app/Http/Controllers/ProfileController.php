<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Repairs;
use App\Models\UserLogs;
use Auth;
use DB;
use App\Models\UserDetail;
use Image;
use App\Models\Files;
use File;

class ProfileController extends Controller
{
    public $id;

    public function __construct() 
    {
       $this->middleware(function ($request, $next) {
            $this->id = Auth::user()->id;
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       $user = User::find($this->id);
       $repairs = Repairs::where('user_id', $this->id)->get();

       $has_meta = DB::table('user_details')->where('user_id', $this->id)->count();

       if( $has_meta > 0 ) {
            $meta = UserDetail::where('user_id', $this->id)->get();
       } else {
            $meta = '';
       }   

        $user_logs = UserLogs::where('user_id', $this->id)
                            ->where('action', '!=', 'user_activity' )
                            ->orderBy('created_at', 'DESC')
                            ->get();

        $activity_logs = UserLogs::where('user_id', $this->id)
                            ->where('action', '=', 'user_activity' )
                            ->orderBy('created_at', 'DESC')
                            ->get();

       return view('profile/index')->with(['user' => $user, 'repairs' => $repairs, 'logs' => $user_logs, 'usermeta' => $meta, 'activities' => $activity_logs ]); 

    }    


    /**
     * Display customer's subscription
     *
     * @return \Illuminate\Http\Response
     */
    public function subscription()
    {
       $user = User::find(Auth::user()->id);

       return view('campaign/customer/subscription')->with(['user' => $user]); 

    } 

     public function updateSubscription(Request $request, $id)
    {
       if( $request->subscribe != '' ) {
         $subscribe = 1;
       } else {
          $subscribe = 0;
       }

       $user = User::where('id', $id)->update(['subscribe' => $subscribe]);

       $request->session()->flash('alert-success', 'Subscription has been successfully updated!');
       return back();

    } 


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

       $user    = User::findorfail($id);
       $repairs = Repairs::where('user_id', $id)->get();

       $has_meta = DB::table('user_details')->where('user_id', $id)->count();

       if( $has_meta > 0 ) {
            $meta = UserDetail::where('user_id', $id)->get();
       } else {
            $meta = '';
       }   
       $user_logs = UserLogs::where('user_id', $id)->get();

       return view('profile/show')->with(['user' => $user, 'repairs' => $repairs, 'logs' => $user_logs, 'usermeta' => $meta ]);    
      
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findorfail($id);

       $has_meta = DB::table('user_details')->where('user_id', $this->id)->count();

       if( $has_meta > 0 ) {
            $meta = UserDetail::where('user_id', $this->id)->get();
       } else {
            $meta = '';
       }   

       return view('profile/edit')->with(['user' => $user, 'usermeta' => $meta]); 

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
        $this->validate($request, [
                'firstname' => 'required',
                'lastname'  => 'required',
                'company'   => 'required',
                'phone'     => 'required',
                'email'     => 'required|email',
                'country'   => 'required',
                'photo'     => 'mimes:jpeg,jpg,png,JPG,PNG,JPEG'


        ]);    

        $old_info = DB::table('users')->where('id', $id)->get();
        

        $user_has_meta = UserDetail::where('user_id', $id)->count();


        DB::table('users')->where('id', $id)->update([
                    'firstname' => strip_tags(  $request->input('firstname') ),
                    'lastname'  => strip_tags( $request->input('lastname') ),
                    'company'   => strip_tags( $request->input('company') ),
                    'phone'     => strip_tags( $request->input('phone') ),
                    'email'     => strip_tags( $request->input('email') ),
                    'country'   => strip_tags( $request->input('country') ),
        ]);

        if( $request->hasFile('photo') ) {

                    // Upload and Save File
                    $file_upload = $request->file('photo');
                    $filename = $file_upload->getClientOriginalName();
                    $filename = time() . '-'. $filename;
                    $filepath = $request->file('photo')->storeAs( 'profile', $filename, 'public');

                    Storage::disk('uploads')->put($filename, file_get_contents($request->file('photo')->getRealPath()));

        } else {
            $filename = '';
        }
 
        $inputs = [ 
                    'firstname'     => strip_tags( $request->input('firstname') ),
                    'lastname'      => strip_tags( $request->input('lastname') ),
                    'company'       => strip_tags( $request->input('company') ),
                    'phone'         => strip_tags( $request->input('phone') ),
                    'email'         => strip_tags( $request->input('email') ),
                    'country'       => strip_tags( $request->input('country') ),
                    'address'       => strip_tags( $request->input('address') ),
                    'address2'      => strip_tags( $request->input('address2') ),
                    'city'          => strip_tags( $request->input('city') ),
                    'state'         => strip_tags( $request->input('state') ),
                    'zipcode'       => strip_tags( $request->input('zipcode') ),
                    'fax'           => strip_tags( $request->input('fax') ),
                    'sms_number'    => strip_tags( $request->input('sms_number') ),
                    'office_phone'  => strip_tags( $request->input('office_phone') ),
                    'website'       => strip_tags( $request->input('website') ),
                    'photo'         => $filename
                ];            

        $this->log_updated_user($old_info, $inputs);

        if( $user_has_meta > 0 ) {
            $this->log_updated_usermeta($id, $inputs);
        } else {
            $meta               = new UserDetail();
            $meta->address      = strip_tags( $request->input('address') );
            $meta->address2     = strip_tags( $request->input('address2') );
            $meta->city         = strip_tags( $request->input('city') );
            $meta->state        = strip_tags( $request->input('state') );
            $meta->zipcode      = strip_tags( $request->input('zipcode') );
            $meta->fax          = strip_tags( $request->input('fax') );
            $meta->sms_number   = strip_tags( $request->input('sms_number') );
            $meta->office_phone = strip_tags( $request->input('office_phone') );
            $meta->website      = strip_tags( $request->input('website') );

            if($request->hasFile('photo')) {
                $meta->photo      = $filename;
            }
            $meta->user_id      =  Auth::user()->id; 
            $meta->save();


             $logs = new UserLogs();   
             $logs->type = '<span class="bg-primary">User details added</span>'; 
             $logs->description = 'Added <strong>' . $meta->address . '</strong> to <strong>address</strong>, ' . 
                                  '<strong>' . $meta->address2 . '</strong> to <strong>address2</strong>, ' . 
                                  '<strong>' . $meta->city . '</strong> to <strong>city</strong>, ' .
                                  '<strong>' . $meta->state . '</strong> to <strong>state</strong>, ' .
                                  '<strong>' . $meta->fax . '</strong> to <strong>fax</strong>, ' .
                                  '<strong>' . $meta->sms_number . '</strong> to <strong>SMS Number</strong>, ' .
                                  '<strong>' . $meta->office_phone . '</strong> to <strong>Office phone</strong>, ' .
                                  '<strong>' . $meta->website . '</strong> to <strong>Website</strong> ';
             $logs->old_value = '';
             $logs->new_value = '';
             $logs->action = 'new';
             $logs->user_id = Auth::user()->id; 
             $logs->created_by = Auth::user()->id; 
             $logs->save();
        }
        
        $request->session()->flash('alert-success', 'Your contact info has been successfully updated!');

        return redirect('/profile');
    
    
    }
   
    /**
     * Logs all user meta changes to the user_meta table
     * @param  array $id     Id of the user
     * @param  array $inputs From the form inputs
     * @return 
     */
    private function log_updated_usermeta($id, $inputs)
    {
        $logs = UserDetail::where('user_id', $id)->get();

            if( !empty($inputs['photo']) )
            {

                 UserDetail::where('user_id', $id)->update([
                        'address'      => strip_tags( $inputs['address']),
                        'address2'     => strip_tags( $inputs['address2']),
                        'city'         => strip_tags( $inputs['city']),
                        'state'        => strip_tags( $inputs['state']),
                        'zipcode'      => strip_tags( $inputs['zipcode']),
                        'fax'          => strip_tags( $inputs['fax']),
                        'sms_number'   => strip_tags( $inputs['sms_number']),
                        'office_phone' => strip_tags( $inputs['office_phone']),
                        'website'      => strip_tags( $inputs['website']),
                        'photo'        => strip_tags( $inputs['photo']),

                ]);
            
            } else {
                UserDetail::where('user_id', $id)->update([
                        'address'      => strip_tags( $inputs['address'] ),
                        'address2'     => strip_tags( $inputs['address2'] ),
                        'city'         => strip_tags( $inputs['city'] ),
                        'state'        => strip_tags( $inputs['state'] ),
                        'zipcode'      => strip_tags( $inputs['zipcode'] ),
                        'fax'          => strip_tags( $inputs['fax'] ),
                        'sms_number'   => strip_tags( $inputs['sms_number'] ),
                        'office_phone' => strip_tags( $inputs['office_phone'] ),
                        'website'      => strip_tags( $inputs['website'] ),

                ]);
            }

           



        foreach( $logs as $row ) {

            if( $inputs['address'] !== $row->address ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>address</strong> was updated from <strong>' . $row->address . '</strong> to <strong>' . $inputs['address'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->address;
                     $logs->new_value = $inputs['address'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['address2'] !== $row->address2 ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';  
                     $logs->description = '<strong>Address2</strong> was updated from <strong>' . $row->address2 . '</strong> to <strong>' . $inputs['address2'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->address2;
                     $logs->new_value = $inputs['address2'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['city'] !== $row->city ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>city</strong> was updated from <strong>' . $row->city . '</strong> to <strong>' . $inputs['city'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->city;
                     $logs->new_value = $inputs['city'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['zipcode'] !== $row->zipcode ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';  
                     $logs->description = '<strong>Zipcode</strong> was updated from <strong>' . $row->zipcode . '</strong> to <strong>' . $inputs['zipcode'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->zipcode;
                     $logs->new_value = $inputs['zipcode'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['state'] !== $row->state ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>State</strong> was updated from <strong>' . $row->state . '</strong> to <strong>' . $inputs['state'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->state;
                     $logs->new_value = $inputs['state'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['fax'] !== $row->fax ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Fax</strong> was updated from <strong>' . $row->fax . '</strong> to <strong>' . $inputs['fax'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->fax;
                     $logs->new_value = $inputs['fax'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['sms_number'] !== $row->sms_number ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';  
                     $logs->description = '<strong>sms_number</strong> was updated from <strong>' . $row->sms_number . '</strong> to <strong>' . $inputs['sms_number'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->sms_number;
                     $logs->new_value = $inputs['sms_number'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['office_phone'] !== $row->office_phone ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Office phone</strong> was updated from <strong>' . $row->office_phone . '</strong> to <strong>' . $inputs['office_phone'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->office_phone;
                     $logs->new_value = $inputs['office_phone'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['website'] !== $row->website ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Website</strong> was updated from <strong>' . $row->website . '</strong> to <strong>' . $inputs['website'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->website;
                     $logs->new_value = $inputs['website'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }

        } 
    }


    /**
     * Logs all user changes to the users table
     * 
     * @param  array $logs 
     * @param  array $inputs from request
     * @return 
     */
    private function log_updated_user($logs, $inputs)
    {


       foreach( $logs as $row ) {

            if( $inputs['firstname'] !== $row->firstname ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Firstname</strong> was updated from <strong>' . $row->firstname . '</strong> to <strong>' . $inputs['firstname'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->firstname;
                     $logs->new_value = $inputs['firstname'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['lastname'] !== $row->lastname ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Lastname</strong> was updated from <strong>' . $row->lastname . '</strong> to <strong>' . $inputs['lastname'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->lastname;
                     $logs->new_value = $inputs['lastname'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['company'] !== $row->company ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Company</strong> was updated from <strong>' . $row->company . '</strong> to <strong>' . $inputs['company'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->company;
                     $logs->new_value = $inputs['company'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['email'] !== $row->email ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';  
                     $logs->description = '<strong>Email</strong> was updated from <strong>' . $row->email . '</strong> to <strong>' . $inputs['email'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->email;
                     $logs->new_value = $inputs['email'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['country'] !== $row->country ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>'; 
                     $logs->description = '<strong>Country</strong> was updated from <strong>' . $row->country . '</strong> to <strong>' . $inputs['country'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->country;
                     $logs->new_value = $inputs['country'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }
            if( $inputs['phone'] !== $row->phone ) {

                     $logs = new UserLogs();   
                     $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';  
                     $logs->description = '<strong>Phone</strong> was updated from <strong>' . $row->phone . '</strong> to <strong>' . $inputs['phone'] . '</strong> by the <strong>user</strong>';
                     $logs->old_value = $row->phone;
                     $logs->new_value = $inputs['phone'];
                     $logs->action = 'update';
                     $logs->user_id = Auth::user()->id; 
                     $logs->created_by = Auth::user()->id; 
                     $logs->save();
            }

        } 

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
