<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;
use App\Mail\RepairNotification;
use App\Models\Software;
use App\Models\Files;
use App\Models\User;
use App\Models\Comment;
use App\Events\TicketChanges;
use App\Models\TicketLog;
use App\Events\NewTaskComment;
use App\Events\NewTask;
use App\Models\SoftwareAttachment;
use App\Models\SoftwareComment;
use Carbon\Carbon;
use App\Events\TaskUpdates;
use App\Models\Product;
use App\Notifications\NewTaskNotify;
use App\Events\TaskAttachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class SoftwareController extends Controller
{
    public $issues;
    public $types;
    public $products;
    public $created_by;
    public $user_notification;
    public $repair_notification;


    public function __construct()
    {

        $this->middleware('auth');


        $this->repair_notification = Option::where('key', 'notification_new_task_email')->value('value');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function filter_user_task(Request $request)
    {

        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }

        // Set view option
        if (request()->has('view')) {
            $view = request('view');
        } else {
            $view = '';
        }

        $user = Auth::user()->id;
        $tickets = Software::where('assigned_to', $user)
            ->where(function ($query) {
                $query->where('resolve', NULL)
                    ->orWhere('resolve', 0);
            })
            ->orderBy('created_at', 'DESC')->paginate($paginate)->appends(['items' => request('items'), 'view' => request('view')]);

        $totalItems = Software::all()->count();



        return view('softwares/user-task')->with([
            'tickets' => $tickets,
            'totalItems' => $totalItems,
            'view' => $view
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function task_submitted()
    {

        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }

        // Set view option
        if (request()->has('view')) {
            $view = request('view');
        } else {
            $view = '';
        }

        $user = Auth::user()->id;



        $tickets = Software::where('user_id', $user)
            ->where(function ($query) {
                $query->where('resolve', NULL)
                    ->orWhere('resolve', 0);
            })
            ->orderBy('created_at', 'DESC')->paginate($paginate)
            ->appends(['items' => request('items'), 'view' => request('view')]);

        $totalItems = Software::all()->count();




        return view('softwares/submitted-task')->with([
            'tickets' => $tickets,
            'totalItems' => $totalItems,
            'view' => $view

        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = 15;
        $view = '';
        $user = Auth::user()->id;

        $tickets = Software::query()->orderBy('created_at', 'DESC');

        if ($items = request('items')) {
            $paginate = $items;
        }

        if ($view = request('view')) {
            $view = $view;
        }

        if ($search = $request->search ?? $request->keywords) {
            $tickets->where(function ($query) use ($search) {
                $product = DB::table('products');
                $user = DB::table('users');

                $query->where('type', 'like', "%$search%")
                    ->orWhere('summary', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('company', 'like', "%$search%")
                    ->orWhere('resolve', 'like', "%$search%")
                    ->orWhere('resolution', 'like', "%$search%");

                $products = $product->select('id')->where('name', 'like', "%$search%");

                if ($products->count() > 0) {
                    $query->orWhereIn('product_id', $products);
                }

                $users = $user->select('id')
                    ->where('firstname', 'like', "%$search%")
                    ->orWhere('lastName', 'like', "%$search%");

                if ($users->count() > 0) {
                    $query->orWhereIn('assigned_to', $users);
                    $query->orWhereIn('user_id', $users);
                }

                if ($date_create = date_create($search)) {
                    $date_formatted = date_format($date_create, 'Y-m-d');
                    $query->orWhereDate(DB::raw("STR_TO_DATE(created_at, '%Y-%m-%d')"), "$date_formatted");
                }
            });
        }

        if ($ticket_no = $request->ticket_no) {
            $tickets->where('id', $ticket_no);
        }

        if ($product_id = $request->product) {
            $tickets->where('product_id', $product_id);
        }

        if ($type = $request->type) {
            $tickets->where('type', $type);
        }

        if ($status = $request->status) {
            $tickets->where('status', $status);
        }

        if (optional(Auth::user())->hasRole('customer')) {
            $company = Auth::user()->company;

            $tickets
                ->where(function ($query) use ($user, $company) {
                    $query->where('user_id', $user)
                        ->orWhere('assigned_to', $user)
                        ->orWhere('company', $company);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                });
        } else {
            $tickets->where(function ($query) {
                $query->where('resolve', NULL)
                    ->orWhere('resolve', 0);
            });
        }

        $totalItems = Software::all()->count();
        $_tickes = $tickets->paginate($paginate);

        $pagination = json_decode(
            json_encode($_tickes)
        )->links;

        return view('softwares/index')->with([
            'tickets' => $_tickes,
            'totalItems' => $totalItems,
            'pagination' => $pagination,
            'view' => $view,
            'totalitems' => $paginate,
        ]);
    }


    public function showResolvedTasks()
    {
        $user_id = Auth::user()->id;

        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }

        if (optional(Auth::user())->isAdmin()) {

            $tickets = Software::where('resolve', 1)
                ->orderBy('created_at', 'DESC')
                ->paginate(20);
        } else {

            $user_company = Auth::user()->company;

            $tickets = Software::where('resolve', 1)
                ->where(function ($query) use ($user_id, $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assigned_to', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate(20);
        }

        $pagination = json_decode(
            json_encode($tickets)
        )->links;

        return view('softwares/resolve')->with(['tickets' => $tickets, 'totalitems' => $paginate, 'pagination' => $pagination]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->issues   = DB::table('issues')->get();
        $this->products = DB::table('products')->get();

        return view('softwares/create')->with(['issues' => $this->issues, 'products' => $this->products]);
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
            'type'    => 'required',
            'summary' => 'required',
            'status'  => 'required',
            'product' => 'required'
        ]);


        if ($request->hasFile('file')) {

            // Upload and Save File
            $file_upload = $request->file('file');
            $filename = $file_upload->getClientOriginalName();

            $getfilename =  str_replace(' ', '_', $filename);
            $path = 'tickets';
            $filename = time() . '-' . $getfilename;
            Storage::disk('local')->makeDirectory($path);

            //$directory = File::makeDirectory($path);
            $filepath = $request->file('file')->storeAs($path, $filename);
            //$filefolder = 'softwares';
            $file_size = File::size($file_upload);
        } else {

            $filename = '';
            $filepath = '';
        }



        $software = new Software;
        $software->type = $request->input('type');
        $software->description = $request->input('description');
        $software->summary = $request->input('summary');
        $software->filename = $filename;
        $software->filepath = $filepath;
        $software->status = $request->input('status');
        $software->assigned_to = $request->input('assigneeID');
        if (optional(Auth::user())->isAdmin()) {
            $company = DB::table('users')->where('id', $request->input('assigneeID'))->value('company');
            $software->company = $company;
        } elseif (!optional(Auth::user())->isAdmin()) {
            $software->company = Auth::user()->company;
        }
        $software->user_id = Auth::user()->id;
        $software->product_id = $request->input('product');
        $software->save();

        event(new NewTask($software));


        $log = new TicketLog;
        $log->software_id = $software->id;
        $log->type = '<span class="box-bg bg-color-6">New Task</span>';
        $log->description = '<strong>New task</strong> has been added by the <strong>SPG user</strong> with user id <strong>#' . Auth::user()->id . '</strong>';
        $log->old_value = '';
        $log->new_value = $software->id;
        $log->action = 'task';
        $log->assigned_to = $request->input('assigneeID');
        $log->created_by = Auth::user()->id;
        $log->save();

        session()->flash('alert-success', 'New Software Feature / Defect has been added!');
        return back();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAjax(Request $request)
    {
        if ($request->ajax()) {
            $this->validate($request, [
                'type'    => 'required',
                'summary' => 'required',
                'status'  => 'required',
                'product' => 'required'
            ]);


            if ($request->hasFile('file')) {

                // Upload and Save File
                $file_upload = $request->file('file');
                $filename = $file_upload->getClientOriginalName();

                $getfilename =  str_replace(' ', '_', $filename);
                $path = 'tickets';
                $filename = time() . '-' . $getfilename;
                Storage::disk('local')->makeDirectory($path);

                //$directory = File::makeDirectory($path);
                $filepath = $request->file('file')->storeAs($path, $filename);
                //$filefolder = 'softwares';
                $file_size = File::size($file_upload);
            } else {

                $filename = '';
                $filepath = '';
            }

            $software = new Software;
            $software->type = strip_tags($request->input('type'));
            $software->description = strip_tags($request->input('description'));
            $software->summary = strip_tags($request->input('summary'));
            $software->filename = $filename;
            $software->filepath = $filepath;
            $software->status = strip_tags($request->input('status'));
            $software->assigned_to = $request->input('assignID');
            if (optional(Auth::user())->isAdmin()) {
                $company = DB::table('users')->where('id', $request->input('assignID'))->value('company');
                $software->company = $company;
            } elseif (!optional(Auth::user())->isAdmin()) {
                $software->company = Auth::user()->company;
            }
            $software->user_id = Auth::user()->id;
            $software->product_id =  strip_tags($request->input('product'));
            $software->save();


            $log = new TicketLog;
            $log->software_id = $software->id;
            $log->type = '<span class="box-bg bg-color-6">New Task</span>';
            $log->description = '<strong>New task</strong> has been added by the <strong>SPG user</strong> with user id <strong>#' . Auth::user()->id . '</strong>';
            $log->old_value = '';
            $log->new_value = $software->id;
            $log->action = 'task';
            $log->assigned_to = strip_tags($request->input('assignID'));
            $log->created_by = Auth::user()->id;
            $log->save();


            event(new NewTask($software));
            User::find($software->assigned_to)->notify(new NewTaskNotify($software->id));

            $admin_task_in_charge = Option::where('key', 'new_task_notification')->value('value');
            if ($admin_task_in_charge) {
                User::where($admin_task_in_charge)->notify(new NewTaskNotify($software->id));
            }

            session()->flash('alert-success', 'New Software Feature / Defect has been added!');
            return response()->json(true);
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
        $ticket = Software::findorfail($id);

        if (!$ticket) {
            return abort(404);
        }
        $comments = DB::table('software_comments')
            ->where('task_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);


        $attachments = SoftwareAttachment::where('task_id', $ticket->id)
            ->orderBy('created_at', 'DESC')->get();

        $logs = TicketLog::where('software_id', $id)
            ->orderBy('created_at', 'DESC')->get();

        auth()->user()->unreadNotifications->markAsRead();

        return view('softwares/show')->with(['ticket' => $ticket, 'comments' => $comments, 'logs' => $logs, 'attachments' => $attachments]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_task(Request $request)
    {
        $id = $request->input('id');

        if ($request->ajax()) {
            $tasks = [];
            $ticket = Software::find($id);;
            $tasks['ticket'] = $ticket;

            $tasks['link'] = url('admin/softwares') . '/' . $ticket->id;
            $tasks['assigned'] = $ticket->assign->firstname . ' ' . $ticket->assign->lastname;
            $tasks['submitter'] = $ticket->user->firstname . ' ' . $ticket->user->lastname;
            $tasks['comments'] = $comments = Comment::where('ticket_id', $ticket->id)
                ->orderBy('created_at', 'DESC')->get();

            $tasks['log'] = $logs = TicketLog::where('software_id', $id)
                ->orderBy('created_at', 'DESC')->get();

            if ($tasks) {
                return Response($tasks);
            }
            return Response('error');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $auth_id = Auth::user()->id;

        $ticket = Software::find($id);

        $is_authorized = Software::where('user_id', '=', $auth_id)
            ->orWhere('assigned_to', '=', $auth_id)
            ->get();


        if (!$is_authorized) {

            return redirect::to('home');
        }
        $this->products = DB::table('products')->get();
        return view('softwares/edit')->with(['ticket' => $ticket, 'products' => $this->products]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resolvingTask($id)
    {
        $auth_id = Auth::user()->id;

        $ticket = Software::find($id);

        $is_authorized = Software::where('user_id', '=', $auth_id)
            ->orWhere('assigned_to', '=', $auth_id)
            ->get();


        if (!$is_authorized) {

            return redirect::to('home');
        }

        $this->products = DB::table('products')->get();
        return view('softwares/resolving')->with(['ticket' => $ticket, 'products' => $this->products]);
    }



    public function resolve(Request $request, $id)
    {
        $this->validate($request, [
            'resolution'    => 'required',
            'resolve'       => 'required',
        ]);

        if (optional(Auth::user())->isAdmin()) {

            $resolve = DB::table('software_tickets')
                ->where('id', $id)
                ->update([
                    'resolve' => 1,
                    'status' => 'Resolved',
                    'resolution' => strip_tags($request->input('resolution')),
                ]);
        } else {

            $user_id  = Auth::user()->id;
            $is_owner = DB::table('software_tickets')->where('user_id', $user_id);

            if ($is_owner) {

                $resolve = DB::table('software_tickets')
                    ->where('id', $id)
                    ->where(function ($query) use ($user_id) {
                        $query->where('user_id', $user_id)
                            ->orWhere('assigned_to', $user_id);
                    })
                    ->update([
                        'resolve' => 1,
                        'resolution' => strip_tags($request->input('resolution')),
                    ]);
            }
        }

        //Please add

        if ($resolve) {

            $time = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

            $log = new TicketLog;
            $log->software_id = $id;
            $log->type = '<span class="box-bg bg-color-2">Resolved</span>';
            $log->description = '<strong>Task</strong> has been resolved by the <strong>SPG user</strong> with user id <strong>#' . Auth::user()->id . '</strong><br> Resolution:' . strip_tags($request->input('resolution')) . ' at ' . $time . '.';
            $log->old_value = '';
            $log->new_value = $id;
            $log->action = 'task';
            $log->assigned_to =  Software::where('id', $id)->value('assigned_to');
            $log->created_by = Auth::user()->id;
            $log->save();

            //send email to admin, assignee and owner

            //Admin Notifcation
            $assigned  = $request->assignee_id;
            $submitter = $request->creator_id;

            $submitter_email = User::find($submitter);

            $date_created = DB::table('software_tickets')->where('id', $id)->value('created_at');

            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $date_created);
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:s:i', $time);

            $diff_in_days = $to->diffInDays($from);
            $diff_in_hours = $to->diffInHours($from);

            $time_resolved = ($diff_in_days == 0) ? $diff_in_hours . ' hours ' : $diff_in_days . ' days';


            $assigned = User::find($assigned);
            $assigned->task_id = $id;
            $assigned->submitter =  $submitter_email;
            $assigned->resolution =  strip_tags($request->input('resolution'));
            $assigned->date_resolved = strval($time_resolved);
            $assigned->resolver = Auth::user()->firstname . ' ' . Auth::user()->lastname;

            event(new TaskUpdates($assigned));

            session()->flash('alert-success', 'Task has been resolved successfully!');

            return back();
        }
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

        $this->validate($request, [
            'type'       => 'required',
            'product'    => 'required',
            'summary'    => 'required',
            'status'     => 'required',
            'assignee'   => 'required',
            'assigneeID' => 'required',
        ]);

        $ticket       = Software::find($id);
        $old_product  = Product::where('id', $ticket->product_id)->value('name');
        $old_assignee = User::where('id', $ticket->assigned_to)->value('id');

        DB::table('software_tickets')
            ->where('id', $id)
            ->update([
                'type'        => strip_tags($request->input('type')),
                'summary'     => strip_tags($request->input('summary')),
                'description' => strip_tags($request->input('description')),
                'status'      => strip_tags($request->input('status')),
                'product_id'  => strip_tags($request->input('product')),
                'assigned_to' => strip_tags($request->input('assigneeID')),
            ]);

        $ticket_id      = $id;
        $spg_id         = Auth::user()->id;
        $assigned_to    = strip_tags($request->input('assigneeID'));


        //$user_id        = Software::where('id', $ticket_id)->value('user_id') );
        $task_submitter = User::find($ticket->user_id);


        // Logging problem description changes
        if (empty($ticket->description) && $request->input('description')) {

            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-1">Description</span>';
            $log->description = '<strong>"' . $request->input('description') . '"</strong> was added to <strong>description</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = '';
            $log->new_value = strip_tags($request->input('description'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            //new description
            $task_submitter->status = 'Description of the task ticket no #' . $ticket_id . ' has been updated  to ' . $request->input('description') . '.';

            event(new TicketChanges($task_submitter));
        } else if ($ticket->description && empty($request->input('description'))) {
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-1">Description</span>';
            $log->description = 'Problem Description was removed from <strong>"' . $ticket->description . '"</strong> to <strong>""</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->description;
            $log->new_value = strip_tags($request->input('description'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            //removing
            $task_submitter->status = 'Description of the task ticket no #' . $ticket_id . ' has been removed';

            event(new TicketChanges($task_submitter));
        } else if ($ticket->description != $request->input('description') &&  !empty($request->input('description'))) {

            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-1">Description</span>';
            $log->description = 'Description was updated from <strong>"' . $ticket->description . '"</strong> to <strong>"' . strip_tags($request->input('description'))  . '"</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->description;
            $log->new_value = strip_tags($request->input('description'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            //Updating
            $task_submitter->status = 'Description of the task ticket  no #' . $ticket_id . ' has been updated from ' . $ticket->description . ' to ' . strip_tags($request->input('description')) . '.';

            event(new TicketChanges($task_submitter));
        }

        if ($ticket->type != $request->input('type')) {
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-5">Task Type changed</span>';
            $log->description = 'The status was updated from <strong>"' . $ticket->type . '"</strong> to <strong>"' . strip_tags($request->input('type'))  . '"</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->assigned_to;
            $log->new_value = $request->input('type');
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();


            $task_submitter->status = 'The Task type of the ticket no #' . $ticket_id . ' has been updated from ' . $ticket->type . ' to ' . $request->input('type') . '.';

            event(new TicketChanges($task_submitter));
        }


        if ($ticket->summary != $request->input('summary')) {

            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-6">Summary</span>';
            $log->description = 'The summary of the task was updated from <strong>"' .  $ticket->summary . '"</strong> to <strong>"' . $request->input('summary')  . '"</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->assigned_to;
            $log->new_value = strip_tags($request->input('product'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            $task_submitter->status = 'The summary of the task no #' . $ticket_id . ' has been updated from ' . $ticket->summary . ' to ' . strip_tags($request->input('summary')) . '.';

            event(new TicketChanges($task_submitter));
        }

        if ($ticket->product_id != $request->input('product')) {

            $new_product = Product::where('id', $request->input('product'))->value('name');

            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-5">Product changed</span>';
            $log->description = 'The product was updated from <strong>"' . $old_product . '"</strong> to <strong>"' . $new_product  . '"</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->assigned_to;
            $log->new_value = strip_tags($request->input('product'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();




            $task_submitter->status = 'The product of the task ticket no #' . $ticket_id . ' has been updated from ' . $old_product . ' to ' . $new_product . '.';

            event(new TicketChanges($task_submitter));
        }


        if ($ticket->status != $request->input('status')) {
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-4">Status changed</span>';
            $log->description = 'The status was updated from <strong>"' . $ticket->status . '"</strong> to <strong>"' . $request->input('status')  . '"</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->assigned_to;
            $log->new_value = strip_tags($request->input('status'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            $task_submitter->status = 'The status of the task ticket no #' . $ticket_id . ' has been updated from ' . $ticket->status . ' to ' . $request->input('status') . '.';

            event(new TicketChanges($task_submitter));
        }


        if ($ticket->assigned_to != $request->input('assigneeID')) {
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-2">Assigned to</span>';
            $log->description = 'The assignee was updated from <strong>user id #' . $ticket->assigned_to . '</strong> to <strong>user id #' . $request->input('assigneeID')  . '</strong> by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = $ticket->assigned_to;
            $log->new_value = strip_tags($request->input('assigneeID'));
            $log->action = 'added';
            $log->assigned_to = strip_tags($request->input('assigneeID'));
            $log->created_by = Auth::user()->id;
            $log->save();

            $task_submitter->status = 'Assignee of task ticket no #' . $ticket_id . ' has been updated';
            $task_submitter->type   = 'submitter';

            // Inform Task Submitter
            event(new TicketChanges($task_submitter));


            // Get the user info of the new assignee
            $new_assignee = User::find($request->input('assigneeID'));

            $new_assignee->status = "You are now in charge to handle the task no #" . $ticket_id . ".";
            $new_assignee->type   = 'new_assignee';

            // Inform new assignee through email
            event(new TicketChanges($new_assignee));


            // Inform old assignee that ticket has been moved
            if ($old_assignee != $request->input('assigneeID')) {

                $user = User::find($old_assignee);

                $user->status = 'The task no #' . $ticket_id . ' has been moved to another member';
                $user->type   = 'old_assignee';

                event(new TicketChanges($user));
            }
        }



        session()->flash('alert-success', 'Task has been successfully updated!');

        return Redirect::to('admin/softwares/' . $id);
    }


    public function advancedSearch(Request $request)
    {

        if (request()->has('items')) {
            $paginate = strip_tags(request('items'));
        } else {
            $paginate = 15;
        }

        $ticket    = strip_tags($request->input('ticket_no'));
        $product   = strip_tags($request->input('product'));
        $type      = strip_tags($request->input('type'));
        $status    = strip_tags($request->input('status'));
        $keywords  = strip_tags($request->input('keywords'));
        $view      = strip_tags($request->input('view'));

        // Set view option
        if (request()->has('view')) {
            $view = request('view');
        } else {
            $view = '';
        }
        if ($ticket && !$product && !$type && !$status && !$keywords) {

            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status  && !$keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && !$status  && $keywords) {
            $software_tickets = Software::where('summary',  'LIKE', '%' . $keywords . '%')
                ->orWhere('description',  'LIKE', '%' . $keywords . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', 'LIKE', '%' . $type . '%');
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } else {
            $software_tickets = DB::table('software_tickets')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        }

        $this->products = DB::table('products')->get();

        return view('softwares/index')->with([
            'products' => $this->products,
            'view' => $view,
            'tickets' => $software_tickets,
            'totalitems' => $paginate
        ]);
    }


    public function advancedSearchUserTask(Request $request)
    {

        if (request()->has('items')) {
            $paginate = strip_tags(request('items'));
        } else {
            $paginate = 15;
        }

        $ticket    = strip_tags($request->input('ticket_no'));
        $product   = strip_tags($request->input('product'));
        $type      = strip_tags($request->input('type'));
        $status    = strip_tags($request->input('status'));
        $keywords  = strip_tags($request->input('keywords'));
        $view      = strip_tags($request->input('view'));
        $id        = Auth::user()->id;

        // Set view option
        if (request()->has('view')) {
            $view = strip_tags(request('view'));
        } else {
            $view = '';
        }


        if ($ticket && !$product && !$type && !$status && !$keywords) {


            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status  && !$keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && !$status  && $keywords) {
            $software_tickets = Software::where('summary',  'LIKE', '%' . $keywords . '%')
                ->orWhere('description',  'LIKE', '%' . $keywords . '%')
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) use ($type) {

                    $query->where('type', 'LIKE', '%' . $type . '%');
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->where(function ($query) use ($id) {
                    $query->where('user_id', $id)
                        ->orWhere('assigned_to', $id);
                })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } else {
            $software_tickets = Software::where(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->orWhere('assigned_to', $id);
            })
                ->where(function ($query) {
                    $query->where('resolve', NULL)
                        ->orWhere('resolve', 0);
                })
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status])
                ->orderBy('created_at', 'DESC');
        }

        $this->products = DB::table('products')->get();

        return view('softwares/index')->with([
            'products' => $this->products,
            'view' => $view,
            'tickets' => $software_tickets,
            'totalitems' => $paginate
        ]);
    }

    public function searchUserTask(Request $request)
    {

        if ($request->ajax()) {

            // URL Parameters
            $id        = Auth::user()->id;
            $status    = strip_tags($request->input('status'));
            $product   = strip_tags($request->input('product'));
            $company   = strip_tags($request->input('company'));
            $items     = strip_tags($request->input('items'));
            $search    = strip_tags($request->input('search'));
            $show_all  = strip_tags($request->input('show_all'));
            $output    = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }

            // Search all products
            $product = DB::table('products')
                ->where('name', $search)
                ->value('id');

            // Company of the current user login   
            $user_company = Auth::user()->company;


            // Search all users            
            $user = DB::table('users')
                ->where('company', $user_company)
                ->where(function ($query) use ($search) {
                    $query->where('firstname', $search)
                        ->orWhere('lastname', $search);
                })
                ->value('id');

            // Get all tasks if search is empty  
            // check if task with same company name          
            if (empty($request->input('search'))) {


                $software_tickets = DB::table('software_tickets')
                    ->where(function ($query) use ($id, $user_company) {
                        $query->where('user_id', $id)
                            ->orWhere('assigned_to', $id)
                            ->orWhere('company', 'LIKE', '%' . $user_company . '%');
                    })
                    // ->where(function( $query ) {
                    //       $query->where('resolve', NULL)
                    //       ->orWhere('resolve', 0);
                    // })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            } else {

                // Search name in products then return the product id
                $search_products =  DB::table('products')->where('name',  'LIKE', '%' . $search . '%')->value('id');

                $software_tickets = DB::table('software_tickets')
                    ->where(function ($query) use ($user, $user_company) {
                        $query->where('user_id', Auth::user()->id)
                            ->orWhere('assigned_to', Auth::user()->id)
                            ->orWhere('company', 'LIKE', '%' .  $user_company . '%');
                    })
                    ->where(function ($query) use ($search, $search_products, $user) {
                        $query->where('type',  'LIKE', '%' . $search . '%')
                            ->orWhere('status',  'LIKE', '%' . $search . '%')
                            ->orWhere('summary',  'LIKE', '%' . $search . '%')
                            ->orWhere('description',  'LIKE', '%' . $search . '%')
                            ->orWhere('user_id', $user)
                            ->orWhere('assigned_to', $user)
                            ->orWhere('product_id',  $search_products)
                            ->orWhere('id', $search);
                    })
                    // ->where(function($query) {
                    //       $query->where('resolve', NULL)
                    //       ->orWhere('resolve', 0);
                    // })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            }



            if ($software_tickets) {
                $page           = $request->input('page');
                $items_per_page = $paginate;
                $view           = $request->input('view');
                $item = 'N/A';


                if ($page) {
                    $start_at = (($page * $items_per_page) - $items_per_page) + 1;
                } else {
                    $start_at = '';
                }

                $count = 0;

                $list = $software_tickets['data'];

                foreach ($software_tickets as $ticket) {

                    $count++;

                    $has_photo   = DB::table('user_details')->where('user_id', $ticket->assigned_to)->value('photo');
                    $photo_link  = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';
                    $photo       = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';

                    $ticket->total_tasks = DB::table('software_tickets')->where('assigned_to', $ticket->assigned_to)->count();
                    $ticket->fullname = DB::table('users')->where('id', $ticket->assigned_to)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->assigned_to)->value('lastname');

                    $ticket->photo_link = asset($photo);


                    $ticket->creator_name = DB::table('users')->where('id', $ticket->user_id)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->user_id)->value('lastname');

                    $has_photo2   = DB::table('user_details')->where('user_id', $ticket->user_id)->value('photo');
                    $photo2       = ($has_photo2) ? '/public/images/uploads/' . $has_photo2 : '/public/images//user-placeholder.png';

                    $ticket->creator_photo_link = asset($photo2);


                    $ticket->link = url('admin/softwares') . '/' . $ticket->id;

                    $ticket->product = Product::find($ticket->product_id)->name;
                }
            } else {
                $software_tickets = 'N/A';
            }

            $response = [
                'tasks' => $software_tickets
            ];

            return $response;
        }
    }


    public function advancedSearchUserResolveTask(Request $request)
    {

        if (request()->has('items')) {
            $paginate = strip_tags(request('items'));
        } else {
            $paginate = 15;
        }

        $ticket    = strip_tags($request->input('ticket_no'));
        $product   = strip_tags($request->input('product'));
        $type      = strip_tags($request->input('type'));
        $status    = strip_tags($request->input('status'));
        $keywords  = strip_tags($request->input('keywords'));
        $view      = strip_tags($request->input('view'));

        $user_company = Auth::user()->company;

        // Set view option
        if (request()->has('view')) {
            $view = request('view');
        } else {
            $view = '';
        }
        if ($ticket && !$product && !$type && !$status && !$keywords) {

            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status  && !$keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && !$status  && $keywords) {
            $software_tickets = Software::where('summary',  'LIKE', '%' . $keywords . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orWhere('description',  'LIKE', '%' . $keywords . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', 'LIKE', '%' . $type . '%');
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } else {
            $software_tickets = DB::table('software_tickets')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($user_company) {
                    $query->where('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        }

        $this->products = DB::table('products')->get();

        return view('softwares/resolve')->with([
            'products' => $this->products,
            'view' => $view,
            'tickets' => $software_tickets,
            'totalitems' => $paginate
        ]);
    }




    public function advancedSearchResolve(Request $request)
    {

        if (request()->has('items')) {
            $paginate = strip_tags(request('items'));
        } else {
            $paginate = 15;
        }

        $ticket    = strip_tags($request->input('ticket_no'));
        $product   = strip_tags($request->input('product'));
        $type      = strip_tags($request->input('type'));
        $status    = strip_tags($request->input('status'));
        $keywords  = strip_tags($request->input('keywords'));
        $view      = strip_tags($request->input('view'));

        // Set view option
        if (request()->has('view')) {
            $view = request('view');
        } else {
            $view = '';
        }
        if ($ticket && !$product && !$type && !$status && !$keywords) {

            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status  && !$keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && !$status  && $keywords) {
            $software_tickets = Software::where('summary',  'LIKE', '%' . $keywords . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orWhere('description',  'LIKE', '%' . $keywords . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && !$status && $keywords) {
            $software_tickets = Software::where('product_id',  'LIKE', '%' . $product . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && !$status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && $type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && !$product && !$type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && !$type && $status && !$keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && !$keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && !$product && $type && $status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && !$status && $keywords) {
            $software_tickets = Software::where('type',  'LIKE', '%' . $type . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && !$type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif (!$ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', $product);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', $type);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } elseif ($ticket && $product && $type && $status && $keywords) {
            $software_tickets = Software::where('id',  'LIKE', '%' . $ticket . '%')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->where(function ($query) use ($type) {

                    $query->where('type', 'LIKE', '%' . $type . '%');
                })
                ->where(function ($query) use ($keywords) {

                    $query->where('summary', 'LIKE', '%' . $keywords . '%')
                        ->orWhere('description', 'LIKE', '%' . $keywords  . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product_id', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        } else {
            $software_tickets = DB::table('software_tickets')
                ->where(function ($query) {
                    $query->where('resolve', 1);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['view' => $view, 'items' => request('items'), 'ticket' => $ticket, 'product' => $product, 'type' => $type, 'status' => $status]);
        }

        $this->products = DB::table('products')->get();

        return view('softwares/resolve')->with([
            'products' => $this->products,
            'view' => $view,
            'tickets' => $software_tickets,
            'totalitems' => $paginate
        ]);
    }







    public function searchTask(Request $request)
    {

        if ($request->ajax()) {

            // URL Parameters
            $id        = Auth::user()->id;
            $status    = strip_tags($request->input('status'));
            $product   = strip_tags($request->input('product'));
            $company   = strip_tags($request->input('company'));
            $items     = strip_tags($request->input('items'));
            $search    = strip_tags($request->input('search'));
            $show_all  = strip_tags($request->input('show_all'));
            $output    = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }

            // Search all products
            $product = DB::table('products')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->value('id');

            // Search all users            
            $user = DB::table('users')
                ->where('firstname',  'LIKE', '%' . $search . '%')
                ->orWhere('lastname',  'LIKE', '%' . $search . '%')
                ->orWhere('company', 'LIKE', '%' . $search . '%')
                ->value('id');

            // Get all tasks if search is empty            
            if (empty($request->input('search'))) {

                $software_tickets = DB::table('software_tickets')
                    // ->where(function($query) {
                    //     $query->where('resolve', NULL)
                    //     ->orWhere('resolve', 0);
                    // })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            } else {

                // Search name in products then return the product id
                $search_products =  DB::table('products')->where('name',  'LIKE', '%' . $search . '%')->value('id');

                $software_tickets = DB::table('software_tickets')
                    ->where(function ($query) use ($search, $search_products, $user) {
                        $query->where('type',  'LIKE', '%' . $search . '%')
                            ->orWhere('id',  'LIKE', '%' . $search . '%')
                            ->orWhere('status',  'LIKE', '%' . $search . '%')
                            ->orWhere('summary',  'LIKE', '%' . $search . '%')
                            ->orWhere('description',  'LIKE', '%' . $search . '%')
                            ->orWhere('product_id',  $search_products)
                            ->orWhere('user_id', $user)
                            ->orWhere('assigned_to', $user);
                    })
                    // ->where(function($query) {
                    //       $query->where('resolve', NULL)
                    //       ->orWhere('resolve', 0);
                    //   })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            }



            if ($software_tickets) {
                $page           = $request->input('page');
                $items_per_page = $paginate;
                $view           = $request->input('view');
                $item = 'N/A';


                if ($page) {
                    $start_at = (($page * $items_per_page) - $items_per_page) + 1;
                } else {
                    $start_at = '';
                }

                $count = 0;

                $list = $software_tickets['data'];

                foreach ($software_tickets as $ticket) {

                    $count++;

                    $has_photo   = DB::table('user_details')->where('user_id', $ticket->assigned_to)->value('photo');
                    $photo_link  = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';
                    $photo       = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';

                    $ticket->total_tasks = DB::table('software_tickets')->where('assigned_to', $ticket->assigned_to)->count();
                    $ticket->fullname = DB::table('users')->where('id', $ticket->assigned_to)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->assigned_to)->value('lastname');

                    $ticket->photo_link = asset($photo);
                    $ticket->link = url('admin/softwares') . '/' . $ticket->id;
                    $ticket->product = Product::find($ticket->product_id)->name;

                    $ticket->creator_name = DB::table('users')->where('id', $ticket->user_id)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->user_id)->value('lastname');

                    $has_photo2   = DB::table('user_details')->where('user_id', $ticket->user_id)->value('photo');
                    $photo2       = ($has_photo2) ? '/public/images/uploads/' . $has_photo2 : '/public/images//user-placeholder.png';

                    $ticket->creator_photo_link = asset($photo2);
                }
            } else {
                $software_tickets = 'N/A';
            }

            $response = [
                'tasks' => $software_tickets
            ];

            return $response;
        }
    }


    /**
     * Resolved Task list for SPG internal user
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function searchResolveTask(Request $request)
    {

        if ($request->ajax()) {

            // URL Parameters
            $id        = Auth::user()->id;
            $status    = strip_tags($request->input('status'));
            $product   = strip_tags($request->input('product'));
            $company   = strip_tags($request->input('company'));
            $items     = strip_tags($request->input('items'));
            $search    = strip_tags($request->input('search'));
            $show_all  = strip_tags($request->input('show_all'));
            $output    = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }

            // Search all products
            $product = DB::table('products')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->value('id');

            // Search all users            
            $user = DB::table('users')
                ->where('firstname',  'LIKE', '%' . $search . '%')
                ->orWhere('lastname',  'LIKE', '%' . $search . '%')
                ->orWhere('company', 'LIKE', '%' . $search . '%')
                ->value('id');

            // Get all tasks if search is empty            
            if (empty($request->input('search'))) {

                $software_tickets = DB::table('software_tickets')
                    ->where(function ($query) {
                        $query->where('resolve', 1);
                    })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            } else {

                // Search name in products then return the product id
                $search_products =  DB::table('products')->where('name',  'LIKE', '%' . $search . '%')->value('id');

                $software_tickets = DB::table('software_tickets')
                    ->where(function ($query) use ($search, $search_products, $user) {
                        $query->where('type',  'LIKE', '%' . $search . '%')
                            ->orWhere('id',  'LIKE', '%' . $search . '%')
                            ->orWhere('status',  'LIKE', '%' . $search . '%')
                            ->orWhere('summary',  'LIKE', '%' . $search . '%')
                            ->orWhere('description',  'LIKE', '%' . $search . '%')
                            ->orWhere('product_id',  $search_products)
                            ->orWhere('user_id', $user)
                            ->orWhere('assigned_to', $user);
                    })
                    ->where(function ($query) {
                        $query->where('resolve', 1);
                    })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            }



            if ($software_tickets) {
                $page           = $request->input('page');
                $items_per_page = $paginate;
                $view           = $request->input('view');
                $item = 'N/A';


                if ($page) {
                    $start_at = (($page * $items_per_page) - $items_per_page) + 1;
                } else {
                    $start_at = '';
                }

                $count = 0;

                $list = $software_tickets['data'];

                foreach ($software_tickets as $ticket) {

                    $count++;

                    $has_photo   = DB::table('user_details')->where('user_id', $ticket->assigned_to)->value('photo');
                    $photo_link  = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';
                    $photo       = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';

                    $ticket->total_tasks = DB::table('software_tickets')->where('assigned_to', $ticket->assigned_to)->count();
                    $ticket->fullname = DB::table('users')->where('id', $ticket->assigned_to)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->assigned_to)->value('lastname');

                    $ticket->photo_link = asset($photo);
                    $ticket->link = url('admin/softwares') . '/' . $ticket->id;
                    $ticket->product = Product::find($ticket->product_id)->name;
                }
            } else {
                $software_tickets = 'N/A';
            }

            $response = [
                'tasks' => $software_tickets
            ];

            return $response;
        }
    }


    /**
     * Resolved Task list for customer / company
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function searchUserResolveTask(Request $request)
    {

        if ($request->ajax()) {

            // URL Parameters
            $id        = Auth::user()->id;
            $status    = strip_tags($request->input('status'));
            $product   = strip_tags($request->input('product'));
            $company   = strip_tags($request->input('company'));
            $items     = strip_tags($request->input('items'));
            $search    = strip_tags($request->input('search'));
            $show_all  = strip_tags($request->input('show_all'));
            $output    = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }

            // Search all products
            $product = DB::table('products')
                ->where('name', 'LIKE', '%' . $search . '%')
                ->value('id');

            // Company of the current user login   
            $user_company = Auth::user()->company;

            // Search all users            
            $user = DB::table('users')
                ->where('company', $user_company)
                ->where(function ($query) use ($search) {
                    $query->where('firstname', $search)
                        ->orWhere('lastname', $search);
                })
                ->value('id');

            // Get all tasks if search is empty            
            if (empty($request->input('search'))) {

                $software_tickets = DB::table('software_tickets')
                    ->where('company', $user_company)
                    ->where(function ($query) {
                        $query->where('resolve', 1);
                    })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            } else {

                // Search name in products then return the product id
                $search_products =  DB::table('products')->where('name',  'LIKE', '%' . $search . '%')->value('id');

                $software_tickets = DB::table('software_tickets')
                    ->where('company', $user_company)
                    ->where(function ($query) use ($search, $search_products, $user) {
                        $query->where('type',  'LIKE', '%' . $search . '%')
                            ->orWhere('id',  'LIKE', '%' . $search . '%')
                            ->orWhere('status',  'LIKE', '%' . $search . '%')
                            ->orWhere('summary',  'LIKE', '%' . $search . '%')
                            ->orWhere('description',  'LIKE', '%' . $search . '%')
                            ->orWhere('product_id',  $search_products)
                            ->orWhere('user_id', $user)
                            ->orWhere('assigned_to', $user);
                    })
                    ->where(function ($query) {
                        $query->where('resolve', 1);
                    })
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            }



            if ($software_tickets) {
                $page           = $request->input('page');
                $items_per_page = $paginate;
                $view           = $request->input('view');
                $item = 'N/A';


                if ($page) {
                    $start_at = (($page * $items_per_page) - $items_per_page) + 1;
                } else {
                    $start_at = '';
                }

                $count = 0;

                $list = $software_tickets['data'];

                foreach ($software_tickets as $ticket) {

                    $count++;

                    $has_photo   = DB::table('user_details')->where('user_id', $ticket->assigned_to)->value('photo');
                    $photo_link  = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';
                    $photo       = ($has_photo) ? '/public/images/uploads/' . $has_photo : '/public/images//user-placeholder.png';

                    $ticket->total_tasks = DB::table('software_tickets')->where('assigned_to', $ticket->assigned_to)->count();
                    $ticket->fullname = DB::table('users')->where('id', $ticket->assigned_to)->value('firstname') . ' ' . DB::table('users')->where('id', $ticket->assigned_to)->value('lastname');

                    $ticket->photo_link = asset($photo);
                    $ticket->link = url('admin/softwares') . '/' . $ticket->id;
                    $ticket->product = Product::find($ticket->product_id)->name;
                }
            } else {
                $software_tickets = 'N/A';
            }

            $response = [
                'tasks' => $software_tickets
            ];

            return $response;
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $task = Software::find($id);
        $task->delete();

        session()->flash('alert-danger', 'Task has been successfully deleted!');
        return redirect('admin/softwares');
    }

    /**
     * Search all users
     * @param  Request $request [description]
     * @return json
     */
    public function searchUser(Request $request)
    {
        if ($request->ajax()) {

            if ($request->input('name') && optional(Auth::user())->isAdmin()) {


                $users = User::where('firstname', 'LIKE', '%' . strip_tags($request->input('name')) . '%')
                    ->orWhere('lastname', 'LIKE', '%' . strip_tags($request->input('name')) . '%')
                    ->orWhere('company', 'LIKE', '%' . strip_tags($request->input('name')) . '%')
                    ->get();


                return Response($users);
            } else if ($request->input('name') && ! optional(Auth::user())->isAdmin()) {

                $company = Auth::user()->company;

                $search = strip_tags($request->input('name'));

                $users = User::where('company', $company)
                    ->where(function ($query) use ($search) {

                        $query->where('firstname', 'LIKE', '%' . $search . '%')
                            ->orWhere('lastname', 'LIKE', '%' . $search . '%');
                    })
                    ->get();

                return Response($users);
            } else {
                $message = '';
                return Response($message);
            }
        }
    }

    public function comment(Request $request)
    {
        $spg_id        = Auth::user()->id;

        $this->validate($request, [
            'description' => 'required'
        ]);

        $ticket_id   = $request->input('task_id');
        $description = strip_tags($request->input('description'));



        if ($request->hasFile('file')) {

            // Upload and Save File
            $ticket_id = $request->input('task_id');


            if ($request->file('file')) {
                // Upload and Save File
                $file_upload = $request->file('file');
                $filename = time() . $file_upload->getClientOriginalName();
                $filepath = $request->file('file')->storeAs('softwares', $filename);
            } else {
                $filename = '';
            }

            $attachment = new SoftwareAttachment();
            $attachment->version = strip_tags($request->input('version'));
            $attachment->description = strip_tags($request->input('description'));
            $attachment->attachments =  $filename;
            $attachment->task_id = strip_tags($request->input('task_id'));
            $attachment->user_id = $spg_id;
            $attachment->save();


            $body           = Option::where('key', 'task_attachment_customer_body')->value('value');
            $task_submitter = User::find($request->input('created_by'));
            $task_submitter->id  =  $request->input('task_id');
            $task_submitter->task_id  =  $request->input('task_id');
            $task_submitter->status = str_replace('[id]', $request->input('task_id'),  $body);

            event(new TaskAttachment($task_submitter));

            $task_assignee = User::find($request->input('assigned_to'));
            $task_assignee->task_id = $request->input('task_id');
            $task_assignee->status = str_replace('[id]', $request->input('task_id'),  $body);

            event(new TaskAttachment($task_assignee));
        } else {
            $filename = '';
        }


        $comment = new SoftwareComment();
        $comment->description = $description;
        if ($filename != '') {
            $comment->attachment_id = ($attachment->id) ? $attachment->id : null;
        }
        $comment->task_id = $ticket_id;
        $comment->user_id = $request->input('created_by');
        $comment->save();

        ////Log new comment 
        $log = new TicketLog;
        $log->software_id = $ticket_id;
        $log->type = '<span class="box-bg bg-color-4">New Comment</span>';
        $log->description = '<strong>Comment #' . $comment->id . '</strong> was added by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
        $log->old_value = '';
        $log->new_value = $request->input('description');
        $log->action = 'comment';
        $log->assigned_to = strip_tags($request->input('assigned_to'));
        $log->created_by = strip_tags($request->input('created_by'));
        $log->save();

        //Log new File
        $log = new TicketLog;
        $log->software_id = $ticket_id;
        $log->type = '<span class="box-bg bg-color-3">File Attachment</span>';
        $log->description = '<strong>New File</strong> was added to comment #' . $comment->id . ' by <strong>SPG User</strong> with user id <strong>#' . $spg_id . '</strong>';
        $log->old_value = '';
        $log->new_value = strip_tags($request->input('description'));
        $log->action = 'attachment';
        $log->assigned_to = strip_tags($request->input('assigned_to'));
        $log->created_by =  strip_tags($request->input('created_by'));
        $log->save();


        /**
         * If current user is the assigned user then nofity through email
         * Or then email the owner
         */
        if ($spg_id === $request->input('assigned_to')) {
            $user = User::find($request->input('created_by'));
            $user->category = 'task';
            $user->ticket_id = $ticket_id;

            event(new NewTaskComment($user));
        } else if ($spg_id === $request->input('created_by')) {

            $repair_creator = User::find($request->input('assigned_to'));
            $repair_creator->category = 'task';
            $repair_creator->ticket_id = $ticket_id;

            event(new NewTaskComment($repair_creator));
        } else {
            // If current is not owner or asignee then email assignee and the owner
            $repair_creator = User::find($request->input('assigned_to'));
            $repair_creator->category = 'task';
            $repair_creator->ticket_id = $ticket_id;

            event(new NewTaskComment($repair_creator));

            $user = User::find($request->input('created_by'));
            $user->category = 'task';
            $user->ticket_id = $ticket_id;

            event(new NewTaskComment($user));
        }

        session()->flash('alert-success', 'New comment has been successfully added!');
        return back();



        if ($request->ajax()) {

            $ticket_id = $request->input('task_id');

            if ($request->hasFile('file')) {

                if ($request->file('file')) {

                    // Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = time() . $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('softwares', $filename);
                } else {
                    $filename = '';
                }

                $attachment = new SoftwareAttachment();
                $attachment->version = strip_tags($request->input('version'));
                $attachment->description = strip_tags($request->input('description'));
                $attachment->attachments =  $filename;
                $attachment->task_id = strip_tags($request->input('task_id'));
                $attachment->user_id = $spg_id;
                $attachment->save();

                $body           = Option::where('key', 'task_attachment_customer_body')->value('value');
                $task_submitter = User::find($request->input('created_by'));

                $task_submitter->status = str_replace('[id]', $request->input('task_id'),  $body);
                event(new TaskAttachment($task_submitter));

                $task_assignee = User::find($request->input('assigned_to'));
                $task_assignee->status = str_replace('[id]', $request->input('task_id'),  $body);
                event(new TaskAttachment($task_assignee));
            }



            //Log Update Warranty in repair_log tbl
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-3">File Attachment</span>';
            $log->description = '<strong>New file</strong> was added by <strong>SPG User</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = '';
            $log->new_value = strip_tags($request->input('description'));
            $log->action = 'attachment';
            $log->assigned_to = strip_tags($request->input('assigned_to'));
            $log->created_by =  strip_tags($request->input('created_by'));
            $log->save();



            if ($request->ajax()) {
                //return response()->json(['success'=>'Record is successfully added']);
                $request->session()->flash('alert-success', 'New attachment has been successfully added!');
                return response()->json(true);
            } else {
                $request->session()->flash('alert-success', 'New attachment has been successfully added!');
                return back();
            }
        }
    }

    /**
     * Get comments of the file revision
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getComments(Request $request)
    {
        if ($request->ajax()) {

            $id = $request->input('id');

            $comments = SoftwareComment::where('attachment_id', $id)->get();

            $output = [];
            $count = 0;

            foreach ($comments as $comment) {


                $output['data'][$count]['id'] = $comment->id;
                $output['data'][$count]['created_at'] = $comment->created_at->diffForHumans();
                $output['data'][$count]['description'] = $comment->description;
                $output['data'][$count]['attachment_id'] = $comment->attachment_id;
                $output['data'][$count]['created_by'] = User::find($comment->user_id)->firstname . ' ' . User::find($comment->user_id)->lastname;

                $photo = DB::table('user_details')->where('user_id', $comment->user_id)->value('photo');

                if ($photo) {
                    $output['data'][$count]['photo'] = asset('public/images/uploads/' . $photo);
                } else {
                    $output['data'][$count]['$photo'] = asset('/public/images//user-placeholder.png');
                }


                $count++;
            }
            $output['length'] = $comments->count();

            return Response($output);
        }
    }

    public function uploadSoftwareFiles($taskID, Request $request)
    {
        $task = Software::find($taskID);

        $image = $request->file('file');

        if ($image) {

            $imageName = $image->getClientOriginalName();
            //$image->move('images', $imageName);

            $filename = time() . $imageName;
            //$imagePath = public_path("images/$imageName");
            $filepath = $image->storeAs('softwares', $filename);
            //$task->files()->create(['attachments'=>$imagePath]);
        }

        return "done";
    }

    public function revision()
    {

        return view('softwares/show/upload-attachment');
    }


    /**
     *  Upload Additional Attachment for a task
     * @param  Request $request 
     * @return Boolean     
     */
    public function uploadAttachment(Request $request)
    {
        $spg_id        = Auth::user()->id;

        //dd($request->all());

        if ($request->ajax()) {

            $ticket_id = $request->input('task_id');

            if ($request->hasFile('file')) {

                if ($request->file('file')) {

                    // Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = time() . $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('softwares', $filename);
                    //$file_size = File::size($file_upload);
                } else {
                    $filename = '';
                }

                $attachment = new SoftwareAttachment();
                $attachment->version = strip_tags($request->input('version'));
                $attachment->description = strip_tags($request->input('description'));
                $attachment->attachments =  $filename;
                $attachment->task_id = strip_tags($request->input('task_id'));
                $attachment->user_id = $spg_id;
                $attachment->save();


                $body           = Option::where('key', 'task_attachment_customer_body')->value('value');
                $task_submitter = User::find($request->input('created_by'));
                $task_submitter->id  =  $attachment->id;
                $task_submitter->status = str_replace('[id]', $request->input('task_id'),  $body);

                event(new TaskAttachment($task_submitter));

                $task_assignee = User::find($request->input('assigned_to'));
                $task_assignee->status = str_replace('[id]', $request->input('task_id'),  $body);
                event(new TaskAttachment($task_assignee));
            }



            //Log Update Warranty in repair_log tbl
            $log = new TicketLog;
            $log->software_id = $ticket_id;
            $log->type = '<span class="box-bg bg-color-3">File Attachment</span>';
            $log->description = '<strong>New file</strong> was added by <strong>SPG User</strong> with user id <strong>#' . $spg_id . '</strong>';
            $log->old_value = '';
            $log->new_value = strip_tags($request->input('description'));
            $log->action = 'attachment';
            $log->assigned_to = strip_tags($request->input('assigned_to'));
            $log->created_by =  strip_tags($request->input('created_by'));
            $log->save();



            if ($request->ajax()) {
                //return response()->json(['success'=>'Record is successfully added']);
                session()->flash('alert-success', 'New attachment has been successfully added!');
                return response()->json(true);
            } else {
                session()->flash('alert-success', 'New attachment has been successfully added!');
                return back();
            }
        }
    }
}
