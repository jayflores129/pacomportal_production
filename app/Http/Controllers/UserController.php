<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Redirect;
use App\Events\ApproveUsers;
use App\Events\DisapproveUsers;
use App\Events\LogUserRegistered;
use App\Models\UserDetail;
use App\Models\UserLogs;
use App\Models\Repairs;
use App\Models\UserCompanies;
use Image;
use App\Models\Files;
use File;
use App\Models\Company;
use App\Models\Role;
use Breadcrumbs;
use App\Models\Software;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role as SpatieRole;


class UserController extends Controller
{
    public $userID;

    public function __construct()
    {
        $this->userID = Auth::id();
    }


    public function index(Request $request)
    {
        $userID = Auth::id();
        $isSpain = Auth::user()->isSpain($userID);
        $roles = SpatieRole::all();

        $sortby = (request()->has('sortby')) ? strip_tags(request('sortby')) : '';
        $sort   = (request()->has('sort')) ? strip_tags(request('sort')) : '';

        $users = User::query();

        if ($sortby && $sort) {
            $users->orderBy($sortby, $sort);
            // ->withCasts('users?sortby='.$sortby.'&sort='. $sort.'');
        } else {
            $users->orderBy('created_at', 'desc');
        }

        if ($isSpain === true) {
            $users->where('country', 'Span');
        }

        if ($search = $request->search) {
            $users->where(function (Builder $builder) use ($search) {
                $builder->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('company', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('country', 'like', "%$search%");
            });
        }

        if ($roleQuery = $request->role) {
            $role = SpatieRole::where('name', $roleQuery)->first();
            $userIds = $role ? $role->users->pluck('id') : collect();
            $users->whereIn('id', $userIds);
        }

        $_users = $users->paginate(20);

        $pagination = json_decode(
            json_encode($_users)
        )->links;

        return view('users/index')->with([
            'users' => $_users,
            'pagination' => $pagination,
            'userID' => Auth::user()->getId(),
            'roles' => $roles,
        ]);
    }


    /**
     * Search for subscribe users
     * @param  Request $request 
     * @return Array          
     */
    public function searchUsers(Request $request)
    {

        if ($request->ajax()) {

            // URL Parameters
            $search    = strip_tags($request->input('search'));

            $output    = "";


            if ($request->input('search')) {

                // Search all users            
                $users = DB::table('users')
                    ->where(function ($query) use ($search) {
                        $query->where('firstname', $search)
                            ->orWhere('lastname', $search)
                            ->orWhere('company', $search);
                    })
                    ->select('id', 'firstname', 'lastname', 'company', 'email', 'blocked', 'status', 'approval_status', 'created_at')
                    ->get();


                foreach ($users as $item) {

                    $item->role = DB::table('role_user')
                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                        ->where('role_user.user_id', '=', $item->id)
                        ->value('roles.name');

                    $item->date =  date('F d, Y', strtotime($item->created_at));
                }
            } else {


                // Search all users            
                $users = DB::table('users')
                    ->select('id', 'firstname', 'lastname', 'company', 'email', 'blocked', 'status', 'approval_status', 'created_at')
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);


                foreach ($users as $item) {

                    $item->role = DB::table('role_user')
                        ->join('roles', 'role_user.role_id', '=', 'roles.id')
                        ->where('role_user.user_id', '=', $item->id)
                        ->value('roles.name');

                    $item->date =  date('F d, Y', strtotime($item->created_at));
                }
            }

            $response = [
                'contacts' => $users,
                'search_for' => $search
            ];

            return $response;
        }
    }

    public function confirm_delete($id = '')
    {
        $user = User::find($id);

        if ($user->isAdmin()) {

            $request->session()->flash('alert-danger', 'User cannot be deleted!');

            return Redirect::to('/thank-you')->with('link', 'user');
        } else {
            $user = User::find($id);

            return Redirect::to('/users/delete')->with('user', $user);
        }
    }

    public function confirmDelete($id)
    {
        $user     = User::find($id);
        $repairs  = Repairs::where('user_id', $id)->get();
        $has_meta = DB::table('user_details')->where('user_id', $id)->count();

        if ($has_meta > 0) {
            $meta = UserDetail::where('user_id', $id)->get();
        } else {
            $meta = '';
        }

        $user_logs = UserLogs::where('user_id', $id)->get();
        $companies = DB::table('companies')->where('name', 'LIKE', '%' . $user->company . '%')->get();

        if (empty($companies) || empty($user->company) || empty($user->company_id)) {
            $companies = Company::all();
        }

        return view('users/confirm-delete')->with(['user' => $user, 'repairs' => $repairs, 'logs' => $user_logs, 'usermeta' => $meta, 'companies' => $companies]);
    }

    public function destroy(Request $request, $id)
    {

        $user = User::find($id);

        if ($user->hasRole('admin') && Auth::user()->hasRole('admin')) {

            $request->session()->flash('alert-danger', 'User cannot be deleted!');

            return Redirect::to('/thank-you')->with('link', 'user');
        } else {

            $user = User::find($id);

            $name = $user->firstname . ' ' . $user->lastname;

            $logs = new UserLogs();
            $logs->type = '<span class="bg-danger">Product Deleted</span>';
            $logs->description =  Auth::user()->firstname . ' ' . Auth::user()->lastname  . ' deleted <strong>' . $user->firstname . ' ' . $user->lastname . '</strong>';
            $logs->old_value = $name;
            $logs->new_value = '';
            $logs->action = 'deleted';
            $logs->user_id = $user->id;
            $logs->created_by = Auth::user()->id;
            $logs->save();


            Software::where('user_id', $id)
                ->orWhere('assigned_to', $id)->delete();

            Repairs::where('user_id', $id)
                ->orWhere('assign_id', $id)->delete();

            User::find($id)->delete();

            $request->session()->flash('alert-success', 'User has been successfully deleted!');

            return redirect()->route('users.index');
        }
    }


    public function show(Request $request, $id)
    {

        $user     = User::findorfail($id);
        $repairs  = Repairs::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $open_repairs  = Repairs::where('user_id', $id)
            ->where('status', 'open')
            ->orderBy('created_at', 'desc')
            ->get();

        $softwares  = Software::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->where('resolve', null)
            ->get();

        $softwares_resolved  = Software::where('user_id', $id)
            ->where('resolve', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        $has_meta = DB::table('user_details')->where('user_id', $id)->count();

        if ($has_meta > 0) {
            $meta = UserDetail::where('user_id', $id)->get();
        } else {
            $meta = '';
        }

        $user_logs = UserLogs::where('user_id', $id)->get();
        $companies = DB::table('companies')->where('name', 'LIKE', '%' . $user->company . '%')->get();
        $userCompanies = UserCompanies::where('user_id', $id)->where('company_id', '<>', null)->get();

        if (empty($companies) || empty($user->company) || empty($user->company_id)) {
            $companies = Company::all();
            $allCompanies = Company::all();
        } else {
            $allCompanies = Company::all();
        }

        $hasCompany = UserCompanies::hasCompany($id);

        return view('users/show')->with([
            'user' => $user,
            'repairs' => $repairs,
            'open_repairs' => $open_repairs,
            'softwares' => $softwares,
            'softwares_resolved' => $softwares_resolved,
            'logs' => $user_logs,
            'usermeta' => $meta,
            'hasCompany' =>  $hasCompany,
            'allCompanies' => $allCompanies,
            'userCompanies' => $userCompanies,
            'companies' => $companies
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = SpatieRole::all();
        $companies = Company::all();

        return view('users/create')->with(['roles' => $roles, 'companies' => $companies]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $user = User::find($id);

        $has_meta = DB::table('user_details')->where('user_id', $id)->count();
        $roles = SpatieRole::all();

        if ($has_meta > 0) {
            $meta = UserDetail::where('user_id', $id)->get();
        } else {
            $meta = '';
        }

        if (!Auth::user()->hasRole('admin')) {

            $request->session()->flash('alert-danger', 'User cannot be edited!');

            return Redirect::to('/thank-you')->with('link', 'user');
        } else {
            return view('users/edit')->with(['user' => $user, 'usermeta' => $meta, 'roles' => $roles,]);
        }
    }


    /**
     * Edit User's Password
     * @param  [type] $id user id
     * @return [type] users info  
     */
    public function customerResetPassword($id)
    {

        $user = User::find($id);

        if (Auth::user() || !$user->hasRole('admin')) {

            return view('users/edit-password')->with(['user' => $user]);
        } else {

            return response()->view('errors.403');
        }
    }


    /**
     * Update user's password
     * @return view
     */
    public function update_password(Request $request, $id)
    {


        $v = Validator::make($request->all(), array(
            'password'  => 'required|min:6',
        ));

        if ($v->passes()) {
            $password         = bcrypt($request->input('password'));
            $password_hint    = strip_tags($request->input('password_hint'));
            $user = DB::table('users')->where('id',  $id)->update(['password' => $password, 'password_hint' => $password_hint]);

            $request->session()->flash('alert-success', 'Password has been successfully updated!');
            return back();
        } else {
            return redirect('admin/password-change')
                ->withErrors($v)
                ->withInput();
        }
    }

    /**
     * Block User from accessing the website
     * @return view
     */
    public function block_user(Request $request, $id)
    {

        $user = DB::table('users')->where('id', $id)->update(['status' => 0, 'blocked' => 1]);


        //Log the activity
        if ($user) {

            $logs = new UserLogs();
            $logs->type = '<span class="box-bg bg-secondary">Blocked user</span>';
            $logs->description = '<strong>User</strong> was blocked from accessing the website <strong> -' . \Carbon\Carbon::now() . ' by the <strong>SPG user</strong> #' . Auth::user()->id . '';
            $logs->old_value = 'unblock';
            $logs->new_value = 'blocked';
            $logs->action = 'update';
            $logs->user_id = $id;
            $logs->created_by = Auth::user()->id;
            $logs->save();
        }

        $request->session()->flash('alert-success', 'User has been successfully blocked!');
        return back();
    }

    /**
     * Get User Information and Company
     * @return view
     */
    public function find_user(Request $request)
    {

        if ($request->ajax() && $request->input('id')) {

            $id = $request->input('id');

            $user = DB::table('users')->select('firstname', 'lastname', 'company', 'phone', 'email', 'company_id', 'id')->where('id', $id)->get();

            $company_id = DB::table('users')->where('id', $id)->value('company_id');

            $company = DB::table('companies')->select('id', 'address', 'telephone_no', 'email', 'name', 'country')->where('id',  $company_id)->get();

            $response = ['user' => $user, 'company' => $company];

            return Response($response);
        }
    }


    /**
     * Block User from accessing the website
     * @return view
     */
    public function unblock_user(Request $request, $id)
    {

        $user = DB::table('users')->where('id', $id)->update(['status' => 1, 'blocked' => 0]);



        $logs = new UserLogs();
        $logs->type = '<span class="box-bg bg-secondary">Blocked user</span>';
        $logs->description = 'This user has been remove from blocked list  <strong> -' . \Carbon\Carbon::now() . ' by the <strong>SPG user</strong> #' . Auth::user()->id . '';
        $logs->old_value = 'unblock';
        $logs->new_value = 'blocked';
        $logs->action = 'update';
        $logs->user_id = $id;
        $logs->created_by = Auth::user()->id;
        $logs->save();


        $request->session()->flash('alert-success', 'User has been successfully unblocked!');
        return back();
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
            'email'     => 'required|unique:users',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required'
        ));
        //$v->passes();   // returns true;

        if ($v->passes()) {

            //$company_name = Company::find( $request->input('company') )->name;

            $user = new User;
            $user->firstname   = strip_tags($request->input('firstname'));
            $user->lastname    = strip_tags($request->input('lastname'));
            $user->email       = strip_tags($request->input('email'));
            $user->company     = $request->input('company') ?? Auth::user()->company_id ?? null;
            $user->country     = strip_tags($request->input('country'));
            $user->phone       = strip_tags($request->input('phone'));
            if ($request->input('subscribe')) {
                $user->subscribe    = 1;
            }
            $user->password    = bcrypt($request->input('password'));
            $user->status      = 0;
            $user->save();

            $auth_user_id = Auth::user()->id;

            $admin_firstname = DB::table('users')->where('id', $auth_user_id)->value('firstname');
            $admin_lastname  = DB::table('users')->where('id', $auth_user_id)->value('lastname');

            $logs = new UserLogs();
            $logs->type = '<span class="box-bg bg-secondary">User Registration</span>';
            $logs->description = '<strong>User</strong> was <strong>created</strong> by ' . $admin_firstname . ' ' . $admin_lastname . '';
            $logs->old_value = 'Pending';
            $logs->new_value = 'Approved';
            $logs->action = 'update';
            $logs->user_id = $user->id;
            $logs->created_by = Auth::user()->id;
            $logs->save();

            // attach role as customer
            $user->assignRole($request->input('role'));

            // listen to this event and do something
            event(new LogUserRegistered($user));
            //listen to this event and do something
            //
            $user->no_notification = true;
            event(new LogUserRegistered($user));



            $request->session()->flash('alert-success', 'New account has been successfully registered!');
            return redirect()->action('UserController@show', ['user' => $user->id]);
        } else {

            return redirect('admin/users/create')
                ->withErrors($v)
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'firstname' => 'required',
            'lastname'  => 'required',
            'company'   => 'required',
            'email'     => 'required|email',
            'country'   => 'required',
            'role'      => 'required'


        ]);

        $old_info = DB::table('users')->where('id', $id)->get();

        //check if user has user meta
        $user_has_meta = UserDetail::where('user_id', $id)->count();

        $user = User::find($id);

        $user->firstname = strip_tags($request->input('firstname'));
        $user->lastname = strip_tags($request->input('lastname'));
        $user->company = strip_tags($request->input('company'));
        $user->phone = strip_tags($request->input('phone'));
        $user->email = strip_tags($request->input('email'));
        $user->country = strip_tags($request->input('country'));

        if ($role = $request->input('role')) {
            $user->assignRole($role);
        }

        $user->save();

        // DB::table('users')->where('id', $id)->update([
        //             'firstname' => strip_tags( $request->input('firstname') ),
        //             'lastname'  => strip_tags( $request->input('lastname') ),
        //             'company'   => strip_tags( $request->input('company') ),
        //             'phone'     => strip_tags( $request->input('phone') ),
        //             'email'     => strip_tags( $request->input('email') ),
        //             'country'   => strip_tags( $request->input('country') ),
        // ]);



        if ($request->hasFile('photo')) {

            // Upload and Save File
            $file_upload = $request->file('photo');
            $filename = $file_upload->getClientOriginalName();
            $filename = time() . '-' . $filename;
            $filepath = $request->file('photo')->storeAs('profile', $filename, 'public');

            Storage::disk('uploads')->put($filename, file_get_contents($request->file('photo')->getRealPath()));
        } else {
            $filename = '';
        }

        $inputs = [
            'firstname'     => strip_tags($request->input('firstname')),
            'lastname'      => strip_tags($request->input('lastname')),
            'company'       => strip_tags($request->input('company')),
            'phone'         => strip_tags($request->input('phone')),
            'email'         => strip_tags($request->input('email')),
            'country'       => strip_tags($request->input('country')),
            'address'       => strip_tags($request->input('address')),
            'address2'      => strip_tags($request->input('address2')),
            'city'          => strip_tags($request->input('city')),
            'state'         => strip_tags($request->input('state')),
            'zipcode'       => strip_tags($request->input('zipcode')),
            'fax'           => strip_tags($request->input('fax')),
            'sms_number'    => strip_tags($request->input('sms_number')),
            'office_phone'  => strip_tags($request->input('office_phone')),
            'website'       => strip_tags($request->input('website')),
            'photo'         => $filename
        ];

        $this->log_updated_user($old_info, $inputs, $id);

        if ($user_has_meta > 0) {
            $this->log_updated_usermeta($id, $inputs);
        } else {
            $meta               = new UserDetail();
            $meta->address      = strip_tags($request->input('address'));
            $meta->address2     = strip_tags($request->input('address2'));
            $meta->city         = strip_tags($request->input('city'));
            $meta->state        = strip_tags($request->input('state'));
            $meta->zipcode      = strip_tags($request->input('zipcode'));
            $meta->fax          = strip_tags($request->input('fax'));
            $meta->sms_number   = strip_tags($request->input('sms_number'));
            $meta->office_phone = strip_tags($request->input('office_phone'));
            $meta->website      = strip_tags($request->input('website'));
            if ($request->hasFile('photo')) {
                $meta->photo      = $filename;
            }
            $meta->user_id      =  $id;
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
            $logs->user_id = $id;
            $logs->created_by = Auth::user()->id;
            $logs->save();
        }


        $request->session()->flash('alert-success', 'User has been successfully updated!');

        return redirect()->action('UserController@show', ['user' => $id]);
    }

    public function updateCompany(Request $request, $id)
    {
        $company_name = Company::where('id', $request->input('company'))->value('name');
        $company_id = Company::where('id', $request->input('company'))->value('id');

        DB::table('users')->where('id', $id)->update([
            'company_id' => strip_tags($request->input('company')),
            'company' => $company_name,
        ]);

        $userComp = new UserCompanies();
        $userComp->user_id = $id;
        $userComp->company_id = $company_id;
        $userComp->primary = true;
        $userComp->save();


        $request->session()->flash('alert-success', 'User has been successfully added to the company!');

        return redirect()->action('UserController@show', ['user' => $id]);
    }

    public function addCompany(Request $request, $id)
    {

        $company_name = Company::where('id', $request->input('company'))->value('name');
        $company_id = Company::where('id', $request->input('company'))->value('id');
        $user_id = $request->input('user_id');


        $hasCompany = UserCompanies::where([['company_id', $company_id], ['user_id', $user_id]])->first();

        if ($hasCompany == true) {
            $request->session()->flash('alert-warning', 'The company was already added!');
        } else {

            $userComp = new UserCompanies();
            $userComp->user_id = $id;
            $userComp->company_id = $company_id;
            $userComp->primary = false;
            $userComp->save();


            $request->session()->flash('alert-success', 'User has been successfully added to the company');
        }

        return redirect()->action('UserController@show', ['user' => $id]);
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
        $auth_user_id = Auth::user()->id;

        foreach ($logs as $row) {

            if ($inputs['address'] !== $row->address) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>address</strong> was updated from <strong>' . $row->address . '</strong> to <strong>' . $inputs['address'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->address;
                $logs->new_value = $inputs['address'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['address2'] !== $row->address2) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Address2</strong> was updated from <strong>' . $row->address2 . '</strong> to <strong>' . $inputs['address2'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->address2;
                $logs->new_value = $inputs['address2'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['city'] !== $row->city) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>city</strong> was updated from <strong>' . $row->city . '</strong> to <strong>' . $inputs['city'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->city;
                $logs->new_value = $inputs['city'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['zipcode'] !== $row->zipcode) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Zipcode</strong> was updated from <strong>' . $row->zipcode . '</strong> to <strong>' . $inputs['zipcode'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->zipcode;
                $logs->new_value = $inputs['zipcode'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['state'] !== $row->state) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>State</strong> was updated from <strong>' . $row->state . '</strong> to <strong>' . $inputs['state'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->state;
                $logs->new_value = $inputs['state'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['fax'] !== $row->fax) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Fax</strong> was updated from <strong>' . $row->fax . '</strong> to <strong>' . $inputs['fax'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->fax;
                $logs->new_value = $inputs['fax'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['sms_number'] !== $row->sms_number) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>sms_number</strong> was updated from <strong>' . $row->sms_number . '</strong> to <strong>' . $inputs['sms_number'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->sms_number;
                $logs->new_value = $inputs['sms_number'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['office_phone'] !== $row->office_phone) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Office phone</strong> was updated from <strong>' . $row->office_phone . '</strong> to <strong>' . $inputs['office_phone'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->office_phone;
                $logs->new_value = $inputs['office_phone'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['website'] !== $row->website) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Website</strong> was updated from <strong>' . $row->website . '</strong> to <strong>' . $inputs['website'] . '</strong> by the <strong>SPG user</strong> #' . $auth_user_id . '';
                $logs->old_value = $row->website;
                $logs->new_value = $inputs['website'];
                $logs->action = 'update';
                $logs->user_id = $id;
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
    private function log_updated_user($logs, $inputs, $id)
    {
        $auth_user_id = Auth::user()->id;

        foreach ($logs as $row) {

            if ($inputs['firstname'] !== $row->firstname) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Firstname</strong> was updated from <strong>' . $row->firstname . '</strong> to <strong>' . $inputs['firstname'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->firstname;
                $logs->new_value = $inputs['firstname'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['lastname'] !== $row->lastname) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Lastname</strong> was updated from <strong>' . $row->lastname . '</strong> to <strong>' . $inputs['lastname'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->lastname;
                $logs->new_value = $inputs['lastname'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['company'] !== $row->company) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Company</strong> was updated from <strong>' . $row->company . '</strong> to <strong>' . $inputs['company'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->company;
                $logs->new_value = $inputs['company'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['email'] !== $row->email) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Email</strong> was updated from <strong>' . $row->email . '</strong> to <strong>' . $inputs['email'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->email;
                $logs->new_value = $inputs['email'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['country'] !== $row->country) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Country</strong> was updated from <strong>' . $row->country . '</strong> to <strong>' . $inputs['country'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->country;
                $logs->new_value = $inputs['country'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
            if ($inputs['phone'] !== $row->phone) {

                $logs = new UserLogs();
                $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
                $logs->description = '<strong>Phone</strong> was updated from <strong>' . $row->phone . '</strong> to <strong>' . $inputs['phone'] . '</strong> by the <strong>SPG user</strong>  #' . $auth_user_id . '';
                $logs->old_value = $row->phone;
                $logs->new_value = $inputs['phone'];
                $logs->action = 'update';
                $logs->user_id = $id;
                $logs->created_by = Auth::user()->id;
                $logs->save();
            }
        }
    }


    public function pending()
    {

        $users = DB::table('users')
            ->where('status', '<', 1)
            ->orWhere('status', '')
            ->orWhere('status', '=', NULL)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);


        $id = Auth::user()->getId();

        auth()->user()->unreadNotifications->markAsRead();

        return view('auth/approval')->with(['users' => $users, 'userID' => $id]);
    }

    public function process_approve(Request $request, $id)
    {

        $user = User::find($id);

        $user->status = strip_tags($request->input('status'));

        $user->save();

        $auth_user_id = Auth::user()->id;

        $admin_firstname = DB::table('users')->where('id', $auth_user_id)->value('firstname');
        $admin_lastname  = DB::table('users')->where('id', $auth_user_id)->value('lastname');

        $logs = new UserLogs();
        $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
        $logs->description = '<strong>User</strong> was <strong>approved</strong> to access the website by ' . $admin_firstname . ' ' . $admin_lastname . '';
        $logs->old_value = 'Pending';
        $logs->new_value = 'Approved';
        $logs->action = 'update';
        $logs->user_id = $id;
        $logs->created_by = Auth::user()->id;
        $logs->save();

        event(new ApproveUsers($user));

        $request->session()->flash('alert-success', 'User has been successfully approved!');

        return redirect()->back();
    }

    /**
     *  Change the permission of the user
     *  
     *  @param    id $id  User id that will be disapproved
     *  @return    
     */
    public function disapprove(Request $request, $id)
    {
        $user = User::find($id);

        $user->status = 0;
        $user->approval_status = 1;

        $user->save();

        event(new DisapproveUsers($user));

        $request->session()->flash('alert-danger', 'User has been disapproved!');


        $auth_user_id = Auth::user()->id;

        $admin_firstname = DB::table('users')->where('id', $auth_user_id)->value('firstname');
        $admin_lastname  = DB::table('users')->where('id', $auth_user_id)->value('lastname');

        $logs = new UserLogs();
        $logs->type = '<span class="box-bg bg-secondary">User field changed</span>';
        $logs->description = '<strong>User</strong> was <strong>disapproved</strong> to access the website by ' . $admin_firstname . ' ' . $admin_lastname . '';
        $logs->old_value = 'Pending';
        $logs->new_value = 'Disapproved';
        $logs->action = 'update';
        $logs->user_id = $id;
        $logs->created_by = Auth::user()->id;
        $logs->save();



        return redirect()->back();
    }

    /**
     *  Change the permission of the user
     *  
     *  @param    object  $object The object to convert
     *  @return    array
     */
    public function handle_permission(Request $request)
    {


        if (Auth::user()->hasRole('admin')) {
            $users = DB::table('users')
                //->join('role_user', 'users.id', '=', 'role_user.user_id')
                ->paginate(10);

            $id = Auth::user()->getId();

            $roles = DB::table('roles')->get();

            return view('auth/permission')->with(['users' => $users, 'userID' => $id, 'roles' => $roles]);
        } 
        
        $request->session()->flash('alert-danger', 'You are not allowed to change this user\'s permission!');

        return Redirect::to('/thank-you')->with('link', 'user');
    }


    /**
     *  Process changing permission
     *  
     *  @param    request, id
     *  @return   thank you page
     */
    public function process_permission(Request $request, $id)
    {
        $table = DB::table('model_has_roles');
        $role = $table->where('model_id', $id);

        $data = [
            'role_id' => $request->permission,
            'model_id' => $id,
            'model_type' => User::class
        ];

        if ($role->first()) {
            $role->update($data);
        } else {
            $table->insert($data);
        }

        $request->session()->flash('alert-success', 'User pemission has been successfully changed!');

        return back();
    }

    /**
     *  Set Technical Documentation Updates
     *  
     *  @param    Request $request
     *  @param    String $name
     *  @return   json
     */
    public function setDocumentationNotification(Request $request)
    {
        $auth = Auth::user();

        if ($user = User::find($auth->id)) {
            $user->{$request->name} = $request->notify;
            $user->save();
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
