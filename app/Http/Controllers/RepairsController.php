<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issues;
use App\Models\Products;
use App\Models\Repairs;
use App\Models\RepairLog;
use Redirect;
use App\Models\Comment;
use App\Mail\RepairNotification;
use App\Mail\RepairConfirmed;
use App\Mail\RepairQuotation;
use App\Mail\RepairStatusMail;
use App\Mail\RepairChanges;
use App\Mail\RepairRmaStatus;
use App\Mail\RepairItemChanges;
use App\Mail\RepairItemDeleted;
use App\Mail\RepairItemAdded;
use App\Mail\RepairNew;
use App\Models\Option;
use App\Events\NewComment;
use App\Models\UserLogs;
use App\Models\User;
use App\Events\RepairStatus;
use Input;
use App\Events\NewTicket;
use App\Models\Company;
use App\Notifications\NewRepairNotify;
use App\Models\RmaTickets;
use App\Models\RmaItems;
use App\Models\RmaItemFaults;
use App\Models\RmaStatus;
use App\Models\RmaLogs;
use App\Models\RmaComments;
use App\Models\RmaTrash;
use App\Models\ItemStatus;
use App\Models\RootCause;
use App\Models\Search;
use App\Models\SearchDefault;
use App\Models\UserCompanies;
use App\Traits\SendEmailRepairUpdates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class RepairsController extends Controller
{
    public $issues;
    private $userID;
    public $products;
    public $created_by;
    public $user_notification;
    public $repair_notification;

    private $companies;
    private $users;
    private $fieldChanges;

    use SendEmailRepairUpdates;

    public function __construct()
    {

        $this->middleware('auth');
        $this->companies   = DB::table('companies')->get();
        $this->issues   = DB::table('issues')->get();
        $this->products = DB::table('products')->get();
        $this->users = DB::table('users')->get();
        $this->fieldChanges = array();
        $this->repair_notification = Option::where('key', 'notification_new_repair_email')->value('value');
    }

    public function index(Request $request)
    {
        $rmaTicket = RmaTickets::query();
        $rma_ids = "";

        if (request()->has('items'))
            $paginate = request('items');
        else $paginate = 20;

        if ($request->order_by && $sort_direction = $request->sort_direction) {
            if ($request->order_by === 'requested_date')
            $rmaTicket->orderBy(DB::raw("STR_TO_DATE(requested_date, '%Y-%m-%d')"), $sort_direction);
            else
                $rmaTicket->orderBy($request->order_by, $sort_direction);
        } else $rmaTicket->orderBy('id', 'desc');

        if (optional(Auth::user())->hasRole('customer')) {
            $rmaTicket->orWhere('requester_email', Auth::user()->email);
        }

        if ($search = $request->search) {
            $rmaTicket->where(function (Builder $builder) use ($search) {
                $builder->where('id', 'like', "%$search%")
                    ->orWhere('requester_name', 'like', "%$search%")
                    ->orWhere('requester_company', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('po_number', 'like', "%$search%")
                    ->orWhere('requester_email', 'like', "%$search%")
                    ->orWhere('requested_date', 'like', "%$search%")
                    ->orWhere(DB::raw("CONCAT('R', id)"), 'like', "%$search%")
                    ->orWhereIn('id', function ($query) use($search) {
                        $query->select('rma_id')
                            ->from((new RmaItems())->getTable())
                            ->where('serial_number', 'like', "%$search%");
                    });
            });
        }

        if ($request->is_search == 1) { 
            $rmaTicket->where(function ($builder) use ($request) {
                if (isset($request->rma_number)) {
                    $builder->where('id', $request->rma_number);
                }
                if (isset($request->requester_id)) {
                    $builder->where('user_id', $request->requester_id);
                }
                if (isset($request->company_id)) {
                    $builder->where('company_id', $request->company_id);
                }
                if (isset($request->country)) {
                    $builder->where('country', $request->country);
                }
                if (isset($request->po_number)) {
                    $builder->where('po_number', $request->po_number);
                }
                if (isset($request->status)) {
                    $builder->where('status', $request->status);
                }

                if (isset($request->serial_number)) {
                    $rmaItems = RmaItems::where('serial_number', $request->serial_number)
                        ->where('rma_id', '!=', NULL)
                        ->get();
                    $rma_ids = collect($rmaItems)->map(fn($rmaItem) => $rmaItem->rma_id);
                    $builder->whereIn('id', $rma_ids);
                }

                if (isset($request->model)) {
                    $rmaItems = RmaItems::where('model', $request->model)
                        ->where('rma_id', '!=', NULL)
                        ->get();
                    $rma_ids = collect($rmaItems)->map(fn($rmaItem) => $rmaItem->rma_id);
                    $builder->whereIn('id', $rma_ids);
                }

                if (isset($request->from) && isset($request->to)) {
                    $date_create_from = date_create($request->from);
                    $date_create_to = date_create($request->to);

                    $builder->whereDate(
                        'requested_date',
                        '>=',
                        date_format($date_create_from, 'Y-m-d')
                    );
                    
                    $builder->whereDate(
                        'requested_date',
                        '<=',
                        date_format($date_create_to, 'Y-m-d')
                    );
                }
            });
        }

        $rma_ids = $rmaTicket->pluck('id');

        $repairs = $rmaTicket->paginate($paginate);

        $pagination = json_decode(
            json_encode($rmaTicket->paginate($paginate))
        )->links;

        return view('repairs/index')->with([
            'issues' => $this->issues,
            'products' => $this->products,
            'repairs' => $repairs,
            'pagination' => $pagination,
            'totalitems' => $paginate,
            'issues' => $this->issues,
            'products' => $this->products,
            'users' => $this->users,
            'companies' => $this->companies,
            'rma_ids' => $rma_ids,
        ]);
    }

    private function filterQuery($rma, $val, $field, $type = '')
    {

        if ($val && $field && $type == '') {
            $rma->where($field, $val);
        } else if ($val && $field && $type == 'date_from') {
            $rma->whereDate($field, '>=', $val);
        } else if ($val && $field && $type == 'date_to') {
            $rma->whereDate($field, '<=', $val);
        }
        return;
    }

    public function show($id, Request $request)
    {
        $repair = RmaTickets::findorfail($id);

        $items = [];

        if (optional(Auth::user())->hasRole(['admin', 'staff']) != true && Auth::user()->company_id != $repair->company_id) {
            //$request->session()->flash('alert-danger', 'Sorry, you were not authorized to view the RMA!');
            return redirect()->route('repairs.index');
        }

        foreach ($repair->items as $item) {
            $item->{'faults'} = RmaItems::findorfail($item->id)->faults;
            array_push($items, $item);
        }

        //comment items
        if (request()->has('c_items')) {
            $c_paginate = request('c_items');
        } else {
            $c_paginate = 10;
        }


        $comments = RmaComments::where('rma_id', $id)
            ->orderBy('created_at', 'DESC')
            ->paginate($c_paginate, ['*'], 'p_comments')->appends('c_items',  request('c_items'));


        $itemstatus = ItemStatus::all();
        $rootcause = RootCause::all();

        //log items            
        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }


        $logs  = RmaLogs::where('rma_id', $id)
            ->orderBy('created_at', 'DESC')
            ->paginate($paginate, ['*'], 'p_logs')->appends('items',  request('items'));

        $isAdmin = optional(Auth::user())->isAdmin();

        $isEditor = optional(Auth::user())->isEditor(Auth::user()->email);

        $products = array_merge(
            DB::table('products')->select('name')->get()->toArray(),
            // RmaTickets::select(DB::raw('CONCAT("R", id, " - ", requester_name) as name'))->get()->toArray()
        );

        return view('repairs/show')->with([
            'issues' => $this->issues,
            'products' => $products,
            'repair' => $repair,
            'comments' => $comments,
            'logs' => $logs,
            'users' => $this->users,
            'itemstatus' => $itemstatus,
            'rootcause' => $rootcause,
            'isAdmin' => $isAdmin,
            'isEditor' => $isEditor

        ]);
    }

    public function rmaQuotation($id)
    {


        $item = RmaTickets::findorfail($id);


        return view('repairs/quotation')->with([
            'repair' => $item,
        ]);
    }

    public function refresh_fault_item(Request $request, $id)
    {
        if ($request->ajax()) {
            if (Auth::user()) {

                $item = RmaItems::findorfail($id);
                $item->faults = RmaItemFaults::find($item->id);

                return response()->json([
                    'item' => $item
                ]);
            };
        };
    }

    public function getItemById(Request $request, $id)
    {
        if ($request->ajax()) {
            $item = RmaItems::findorfail($id);
        }

        $response = [
            'item' => $item
        ];
        return $response;
    }

    public function create()
    {
        $lastInsertedID = DB::table('repairs')->orderBy('created_at', 'desc')->first();

        if (optional(Auth::user())->hasRole(['admin', 'staff', 'customer'])) {

            return view('repairs/create')->with(['issues' => $this->issues, 'products' => $this->products]);
        } else {
            return response()->view('errors.403');
        }
    }

    public function createRMA()
    {
        $lastInsertedID = DB::table('repairs')->orderBy('created_at', 'desc')->first();

        if (optional(Auth::user())->hasRole(['admin', 'staff', 'customer'])) {
            $products = array_merge(
                DB::table('products')->select('name')->get()->toArray(),
                // RmaTickets::select(DB::raw('CONCAT("R", id, " - ", requester_name) as name'))->get()->toArray()
            );

            // return response()->json($products);

            return view('repairs/new-create')
                    ->with([
                        'issues' => $this->issues, 
                        'products' => $products, 
                        'users' => $this->users,
                    ]);
        } else {
            return response()->view('errors.403');
        }
    }

    public function viewCreateRMA()
    {
        $lastInsertedID = DB::table('repairs')->orderBy('created_at', 'desc')->first();
        $userID = Auth::user()->id;
        $user = User::find($userID);
        if (optional(Auth::user())->hasRole(['admin', 'staff', 'customer'])) {
            $userCompanies = UserCompanies::where('user_id', Auth::user()->id)->get();
            return view('repairs/show/customer/rma-create')->with(['issues' => $this->issues, 'products' => $this->products, 'user' => $user, 'userCompanies' => $userCompanies]);
        } else {
            return response()->view('errors.403');
        }
    }

    public function add_comment(Request $request)
    {

        $spg_fname     = Auth::user()->firstname;
        $spg_lname     = Auth::user()->lastname;
        $spg_id        = Auth::user()->id;
        $spg_user_link = url('/profile') . '/' . $spg_id;

        $this->validate($request, [
            'comment' => 'required'
        ]);


        $comment = new Comment();
        $comment->description = $request->input('comment');
        $comment->rma_no = $request->input('rma');
        $comment->ticket_id = $request->input('rma');
        $comment->user_id = $request->input('assign_id');
        $comment->created_by = Auth::id();


        $comment->save();


        //Log Update Warranty in repair_log tbl
        $log = new RepairLog;
        $log->repair_id = $request->input('repair_id');
        $log->type = '<span class="box-bg bg-color-4">New Comment</span>';
        $log->description = '<strong>New Comment</strong> was added by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
        $log->old_value = '';
        $log->new_value = $request->input('comment');
        $log->action = 'comment';
        $log->user_id = $request->input('user_id');
        $log->created_by = Auth::user()->id;
        $log->save();

        if ($request->input('assign_id')) {

            $repair_creator = User::find($request->input('assign_id'));
            $repair_creator->ticket_id = $request->input('repair_id');
            $repair_creator->not_assign = false;

            event(new NewComment($repair_creator));
        } else {

            $repair_creator = Company::find($request->input('company_id'));
            $repair_creator->ticket_id = $request->input('repair_id');
            $repair_creator->not_assign = true;

            event(new NewComment($repair_creator));
        }


        //Log Update Warranty in repair_log tbl
        $log = new RepairLog;
        $log->repair_id = $request->input('repair_id');
        $log->type = '<span class="box-bg bg-color-6">Email Sent</span>';
        $log->description = '<strong>New Comment Notification</strong> was sent to ' . $repair_creator->email  . ' by <strong>SPG internal user</strong> with user id <strong>#' . $spg_id . '</strong>';
        $log->old_value = '';
        $log->new_value = $request->input('comment');
        $log->action = 'comment';
        $log->user_id = $request->input('user_id');
        $log->created_by = Auth::user()->id;
        $log->save();

        // $request->session()->flash('alert-success', 'New comment has been successfully added!');

        // // $request->session()->flash('alert-success', 'Ticket has been successfully updated!');

        // $url.= $_SERVER['HTTP_HOST'];   

        // // Append the requested resource location to the URL   
        // $url.= $_SERVER['REQUEST_URI'];    

        // // return redirect('repairs/' . $id);
        // return redirect($url);
        return back();
    }

    public function advancedSearch(Request $request)
    {

        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }

        $company = $request->input('company_name');
        $product = $request->input('product');
        $issue   = $request->input('issue');
        $status  = $request->input('status');


        if ($company && !$product && !$issue && !$status) {

            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
            } elseif (!$company && $product && !$issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('status',  'LIKE', '%' . $status . '%')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && !$issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', 'LIKE', '%' . $issue . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } else {
            $repairs = DB::table('repairs')
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        }


        return view('repairs/index')->with(['issues' => $this->issues, 'products' => $this->products, 'repairs' => $repairs, 'totalitems' => $paginate, 'issues' => $this->issues]);
    }

    public function advancedSearchCustomer(Request $request)
    {

        if (request()->has('items')) {
            $paginate = request('items');
        } else {
            $paginate = 15;
        }

        $company  = $request->input('company_name');
        $product  = $request->input('product');
        $issue    = $request->input('issue');
        $status   = $request->input('status');
        $user_id  = Auth::user()->id;

        $user_company = Auth::user()->company;


        if ($company && !$product && !$issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && !$issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('status',  'LIKE', '%' . $status . '%')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && !$issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('product',  'LIKE', '%' . $product . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && !$product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && $issue && !$status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && !$product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', $issue);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && !$issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif (!$company && $product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('issue',  'LIKE', '%' . $issue . '%')
                ->where(function ($query) use ($product) {

                    $query->where('product', $product);
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', $status);
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } elseif ($company && $product && $issue && $status) {
            $repairs = DB::table('repairs')
                ->where('company',  'LIKE', '%' . $company . '%')
                ->where(function ($query) use ($issue) {

                    $query->where('issue', 'LIKE', '%' . $issue . '%');
                })
                ->where(function ($query) use ($product) {

                    $query->where('product', 'LIKE', '%' . $product . '%');
                })
                ->where(function ($query) use ($status) {

                    $query->where('status', 'LIKE', '%' . $status . '%');
                })
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')
                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        } else {
            $repairs = DB::table('repairs')
                ->where(function ($query) use ($user_id,  $user_company) {
                    $query->where('user_id', $user_id)
                        ->orWhere('assign_id', $user_id)
                        ->orWhere('company', $user_company);
                })
                ->orderBy('created_at', 'DESC')

                ->paginate($paginate)->appends(['items' => request('items'), 'company_name' => $company, 'product' => $product, 'issue' => $issue, 'status' => $status]);
        }


        return view('repairs/index')->with(['issues' => $this->issues, 'products' => $this->products, 'repairs' => $repairs, 'totalitems' => $paginate, 'issues' => $this->issues]);
    }

    public function searchRepair(Request $request)
    {

        if ($request->ajax() && optional(Auth::user())->isAdmin()) {

            $id      = Auth::user()->id;
            $status  = $request->input('status');
            $product = $request->input('product');
            $company = $request->input('company');
            $items   = $request->input('items');
            $search  = $request->input('search');
            $page    =
                $output  = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }
            if (request()->has('page')) {
                $page = request('page');
            } else {
                $page = 1;
            }

            $output = "";

            if (empty($request->input('search'))) {

                $repairs = DB::table('repairs')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items'), 'page' => $page]);;
            } else if (!empty($request->input('search'))) {

                $repairs = DB::table('repairs')
                    ->where('id',  'LIKE', '%' . $search . '%')
                    ->orWhere('company', 'LIKE', '%' .  $search . '%')
                    ->orWhere('issue', 'LIKE', '%' .  $search . '%')
                    ->orWhere('product', 'LIKE', '%' . $search . '%')
                    ->orWhere('product_serial_no', 'LIKE', '%' . $search . '%')
                    ->orWhere('problem_description', 'LIKE', '%' . $search . '%')
                    ->orWhere('issue', 'LIKE', '%' . $search . '%')
                    ->orWhere('status', 'LIKE', '%' . $search . '%')
                    ->orderBy('created_at', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items'), 'page' => $page]);
            } else {
                $repairs = '';
            }

            if ($repairs) {
                foreach ($repairs as $repair) {

                    $repair->is_warranty = ($repair->under_warranty) ? '<span class="fa fa-check"></span>' : '';
                    $repair->status_color = $this->statusColor($repair->status);

                    $repair->link = url('admin/repairs') . '/' . $repair->id;
                }
            } else {
                $repairs = 'N/A';
            }

            $response = [
                'repairs' => $repairs
            ];

            return $response;
        }
    }

    public function searchUserRepair(Request $request)
    {

        if ($request->ajax()) {

            $id      = Auth::user()->id;
            $status  = $request->input('status');
            $product = $request->input('product');
            $company = $request->input('company');
            $items   = $request->input('items');
            $search  = $request->input('search');

            $output = "";

            // Set pagination 
            if (request()->has('items')) {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }
            if (request()->has('page')) {
                $page = request('page');
            } else {
                $page = 1;
            }

            $user_company = Auth::user()->company;

            if (empty($request->input('search'))) {

                $repairs = RmaTickets::where(function ($query) use ($id, $user_company) {
                    $query->where('user_id', $id);
                })
                    ->orderBy('id', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items')]);
            } else if (!empty($request->input('search'))) {

                $repairs = RmaTickets::where(function ($query) use ($id,  $user_company) {
                    $query->where('user_id', $id);
                })
                    ->where(function ($query) use ($search) {

                        $query->where('id',  'LIKE', '%' . $search . '%')
                            ->orWhere('requester_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('company_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('country', 'LIKE', '%' . $search . '%')
                            ->orWhere('requester_phone', 'LIKE', '%' . $search . '%')
                            ->orWhere('status', 'LIKE', '%' . $search . '%')
                            ->orWhere('requester_email', 'LIKE', '%' . $search . '%')
                            ->orWhere('po_number', 'LIKE', '%' . $search . '%');
                    })

                    ->orderBy('id', 'DESC')
                    ->paginate($paginate)->appends(['items' => request('items'), 'page' => $page]);
            }

            if ($repairs) {
                foreach ($repairs as $repair) {

                    //$repair->is_warranty = ( $repair->under_warranty ) ? '<span class="fa fa-check"></span>': '';
                    //$repair->status_color = $this->statusColor($repair->status);
                    $repair->totalItems = $repair->items->count();
                    //$repair->link = url('admin/repairs') . '/' . $repair->id; 

                }
            } else {
                $repairs = 'N/A';
            }

            $response = [
                'repairs' => $repairs
            ];

            return $response;
        }
    }

    function statusColor($status)
    {
        switch ($status) {
            case "open":
                $color = "btn-default btn-open";
                break;

            case "Partially Shipped":
                $color = "btn-default btn-ps";
                break;

            case "Completely Shipped":
                $color = "btn-default btn-cs";
                break;

            case "received":
                $color = "btn-default btn-r";
                break;

            case "repaired":
                $color = "btn-default btn-rp";
                break;

            case "returned":
                $color = "btn-default btn-rt";
                break;
            case "shipped":
                $color = "btn-default btn-rt";
                break;

            default:
                $color = "";
                break;
        }

        return $color;
    }

    public function store(Request $request)
    {



        if ($request->ajax()) {

            if (optional(Auth::user())->hasRole(['admin', 'super admin', 'customer', 'SPG Internal User'])) {

                // Validating Input Fields
                $this->validate($request, [
                    'company' => 'required',

                ]);


                if (optional(Auth::user())->isAdmin()) {
                    $company = $request->input('company');
                } else {
                    $company = Auth::user()->company;
                }
                $product = $request->input('product');
                $serial  = $request->input('serial');
                $issue   = $request->input('issue');
                $rma     = 0;


                $created_by = Auth::user()->id;

                //saving data
                $repair = new Repairs;
                $repair->company = $company;
                $repair->product = $product;
                $repair->product_serial_no = $serial;
                $repair->issue =  $issue;

                $repair->status = 'open';
                $repair->created_by = $created_by;
                $repair->save();


                //Log New repair in repair_log tbl
                $log = new RepairLog;
                $log->repair_id = $repair->id;
                $log->changes = 'Repair Created';
                $log->created_by = Auth::user()->id;
                $log->save();

                try {
                    Mail::to(Auth::user()->email)->send(new RepairNotification($repair));
                    //admin
                    Mail::to($this->repair_notification)->send(new RepairNotification($repair));
                }
                catch (\Exception $e) {}

                return Response('saved');
            } else {
                return Response('Not Authorized');
            }
        }
    }

    public function storeRMA(Request $request)
    {
        if ($request->ajax()) {

            if (Auth::user()) {
                $req_company_id = $request->company_id ?? 0;
                $req_notify = $request->notify ?? 0;
                $req_assign_id = $request->user_id;

                $insert_rma_data = [
                    'status' => 'Open',
                    'added_by' => Auth::user()->id,
                    'user_id' => $req_assign_id,
                    'company_id' => $req_company_id,
                    'notify' => $req_notify,
                ];

                if ($request->date_requested) {
                    $insert_rma_data['requested_date'] = $request->date_requested;
                }
                if ($request->requester_name) {
                    $insert_rma_data['requester_name'] = $request->requester_name;
                }
                if ($request->requester_phone) {
                    $insert_rma_data['requester_phone'] = $request->requester_phone;
                }
                if ($request->requester_company) {
                    $insert_rma_data['requester_company'] = $request->requester_company;
                }
                if ($request->requester_email) {
                    $insert_rma_data['requester_email'] = $request->requester_email;
                }
                if ($request->po_number) {
                    $insert_rma_data['po_number'] = $request->po_number;
                }
                if ($request->requester_fax) {
                    $insert_rma_data['requester_fax'] = $request->requester_fax;
                }
                if ($request->company_name) {
                    $insert_rma_data['company_name'] = $request->company_name;
                }
                if ($request->company_phone) {
                    $insert_rma_data['company_phone'] = $request->company_phone;
                }
                if ($request->company_fax) {
                    $insert_rma_data['company_fax'] = $request->company_fax;
                }
                if ($request->company_address) {
                    $insert_rma_data['company_address'] = $request->company_address;
                }
                if ($request->country) {
                    $insert_rma_data['country'] = $request->country;
                }
                if ($request->currency) {
                    $insert_rma_data['currency'] = $request->currency;
                }

                $response_data = [
                    'success' => true,
                    'error' => null,
                    'id' => null,
                ];

                if (!empty($insert_rma_data) && $rma_ticket_id = DB::table('rma_tickets')->insertGetId($insert_rma_data)) {
                    $repair = RmaTickets::find($rma_ticket_id);

                    $response_data['id'] = $rma_ticket_id;
                    $rma_faults = json_decode($request->items);

                    foreach ($rma_faults as $fault) {
                        if ($fault->repair_cost == '' || $fault->repair_cost == null) {
                            $repair_cost = 0;
                        } else {
                            $repair_cost = $fault->repair_cost;
                        }

                        $rma_fault_fault_id = DB::table('rma_items')->insertGetId([
                            'rma_id' => $rma_ticket_id,
                            'original_order_date' => $fault->original_order_date,
                            'invalid_serial_number' => $fault->invalid_serial,
                            'repair_cost' => $repair_cost,
                            'serial_number' => $fault->serial,
                            'date_purchased' => $fault->date_purchase_known,
                            'under_warranty' => ($fault->under_warranty) ? $fault->under_warranty : null,
                            'model' => $fault->product,
                            'pacom_comment' => $fault->fault_comment,
                        ]);

                        foreach ($fault->fault_cat as $fault) {
                            DB::table('rma_item_faults')->insert([
                                'rma_items_id' => $rma_fault_fault_id,
                                'fault' => $fault,
                            ]);
                        }
                    }

                    $rma_status = new RmaStatus;
                    $rma_status->status = 'Requested by' . ' ' . $request->input('requester_name') .  ' on ' . $request->input('date_requested');
                    $rma_status->updated_by = Auth::user()->id;
                    $rma_status->rma_id = $rma_ticket_id;
                    $rma_status->save();

                    try {
                        if ($req_notify == 1) {
                            $requester_email = RmaTickets::where('id',$rma_ticket_id)->value('requester_email');
                            Mail::to($requester_email)->send(new RepairNew($repair));
                        }
                    }
                    catch(\Exception $e) {
                        $response_data['error'] = $e->getMessage();
                    }
                }

                return response()->json($response_data);

            } else {
                return Response('Not Authorized');
            }
        }
    }

    public function createRMAByCust(Request $request)
    {


        if ($request->ajax()) {

            if (Auth::user()) {


                $assign_id      = $request->input('user_id');
                //$has_company_id = DB::table('users')->where('id', $assign_id)->value('company_id');

                $rma_ticket_id = DB::table('rma_tickets')->insertGetId([
                    'requested_date'      => $request->input('date_requested'),
                    'requester_name'      => $request->input('requester_name'),
                    'requester_phone'      => $request->input('requester_phone'),
                    'requester_company'   => $request->input('requester_company'),
                    'requester_email'     => $request->input('requester_email'),
                    'po_number'           => $request->input('po_number') ?? '',
                    'requester_fax'       => $request->input('requester_fax') ?? '',
                    'country'             => $request->input('country') ?? '',
                    'currency'            => $request->input('currency') ?? '',
                    'company_name'        => $request->input('company_name') ?? '',
                    'company_phone'       => $request->input('company_phone') ?? '',
                    'company_fax'         => $request->input('company_fax') ?? '',
                    'company_address'     => $request->input('company_address') ?? '',
                    'user_id'             => $assign_id,
                    'company_id'          => $request->input('company_id') ?? '',
                    'status'              => 'Open',
                    'notify'              => $request->input('notify') ?? 0,
                    'added_by'             => Auth::user()->id,
                ]);

                $repair = RmaTickets::find($rma_ticket_id);
                try {
                    Mail::to($this->repair_notification)->send(new RepairNew($repair));
                } catch(\Exception $e) {}

                if ($rma_ticket_id) {
                    $rma_faults = json_decode($request->input('items'));

                    $item_faults = [];

                    foreach ($rma_faults as $fault) {


                        $rma_fault_fault_id = DB::table('rma_items')->insertGetId([
                            'rma_id' => $rma_ticket_id,
                            'serial_number' => $fault->serial,
                            'model' => $fault->product,
                            'fault_described_by_customer' => $fault->fault_comment,
                        ]);

                        foreach ($fault->fault_cat as $fault) {
                            DB::table('rma_item_faults')->insert([
                                'rma_items_id' => $rma_fault_fault_id,
                                'fault' => $fault,
                            ]);
                        }
                    }

                    $rma_status = new RmaStatus;
                    $rma_status->status = 'Requested by' . ' ' . $request->input('requester_name') .  ' on ' . $request->input('date_requested');
                    $rma_status->updated_by = Auth::user()->id;
                    $rma_status->rma_id = $rma_ticket_id;
                    $rma_status->save();
                }


                return response()->json([
                    'success' => true,
                    'id' => $rma_ticket_id
                ]);
            } else {
                return Response('Not Authorized');
            }
        }
    }

    public function storeData(Request $request)
    {

        if ($request->ajax()) {

            if (Auth::user()) {

                $assign_id      = $request->input('user_id');
                $has_company_id = DB::table('users')->where('id', $assign_id)->value('company_id');


                $company     = strip_tags($request->input('company'));
                $product     = strip_tags($request->input('product'));
                $description = strip_tags($request->input('description'));
                $serial      = strip_tags($request->input('serial'));
                $issue       = strip_tags($request->input('issue'));

                //saving data
                $repair = new Repairs;
                if (empty($company)) {
                    $repair->company = Auth::user()->company;
                } else {
                    $repair->company = $company;
                }
                $repair->product = $product;
                $repair->product_serial_no = $serial;
                $repair->issue =  $issue;

                if ($has_company_id) {
                    $repair->company_id = $has_company_id;
                }
                if ($assign_id) {
                    $repair->assign_id = $assign_id;
                }
                if ($description) {
                    $repair->description = $description;
                }
                $repair->status = 'open';
                $repair->user_id = Auth::user()->id;
                $repair->save();

                event(new NewTicket($repair));

                /**
                 *  Bell Notifications for assignee, company
                 */
                User::find($assign_id)->notify(new NewRepairNotify($repair->id));


                $rma = $repair->id;
                $created_by = Auth::user()->id;

                //Log New repair in repair_log tbl
                $log = new RepairLog;
                $log->repair_id = $repair->id;
                $log->type = '<span class="box-bg bg-color-1">New repair created</span>';
                $log->description = 'Repair under ticket no #' . $rma  . ' was created by SPG user with id no #<strong>' . $created_by . '</strong>';
                $log->old_value = '';
                $log->new_value = $rma;
                $log->action = 'new';
                $log->user_id = $created_by;
                $log->created_by = $created_by;
                $log->save();


                session()->flash('alert-success', 'New Repair has been successfully added!');

                return Response('saved');
            } else {
                return Response('Not Authorized');
            }
        }
    }

    public function destroy($id)
    {

        // if(Auth::user()->isAdmin()) {

        $RMAticket = RmaTickets::find($id);

        $deleted_by   = Auth::user()->firstname . ' ' . Auth::user()->lastname;

        $data = json_encode($RMAticket);

        $RMATrash =  new RmaTrash;

        $RMATrash->data = $data;
        $RMATrash->deleted_by = $deleted_by;
        $saved = $RMATrash->save();

        $repair = RmaTickets::find($id)->delete();
        return redirect()->route('repairs.index');

        // }
        // else {
        //     return response()->view('errors.403');
        // }
    }

    public function edit($id)
    {

        $repair_owner = RmaTickets::where('id', $id)->value('user_id');

        $repair =  RmaTickets::find($id);


        if (optional(Auth::user())->isAdmin() || Auth::user()->company_id == $repair->company_id && $repair->cust_can_edit !=  0) {
            return view('repairs/edit')
                ->with([
                    'issues' => $this->issues, 
                    'products' => $this->products, 
                    'repair' => $repair
                ]);
        } else {

            return response()->view('errors.403');
        }
    }

    public function customerEditRepair($id)
    {

        $repair_owner = RmaTickets::where('id', $id)->value('user_id');

        $repair = RmaTickets::find($id);


        if (Auth::user()->company_id  == $repair->company_id && $repair->cust_can_edit !=  0) {

            return view('repairs/edit')->with(['issues' => $this->issues, 'products' => $this->products, 'repair' => $repair]);
        } else {

            return response()->view('errors.403');
        }
    }


    /**
     * Update repair information (only for SPG internal users)
     * @param  Request $request From the form request
     * @param  int     $id      Unique id of the repair from the repair table
     * @return 
     */
    public function customerUpdateRepair(Request $request, $id)
    {
        $repair_owner  = Repairs::where('id', $id)->value('user_id');
        $company_ticket =  Repairs::where('id', $id)->value('company');
        $owner         = User::find($repair_owner);
        $spg_fname     = Auth::user()->firstname;
        $spg_lname     = Auth::user()->lastname;
        $spg_id        = Auth::user()->id;
        $spg_user_link = url('/profile') . '/' . $spg_id;

        if (Auth::user()->company == $company_ticket) {
            $this->validate($request, [
                'company' => 'required',
                'product' => 'required',
                'serial_no' => 'required',
                'issue' => 'required'
            ]);

            $repair = Repairs::find($id);

            $repairPD = $repair->problem_description;

            $repairs = DB::table('repairs')
                ->where('id', $id)
                ->update([
                    'product' => strip_tags($request->input('product')),
                    'product_serial_no' => strip_tags($request->input('serial_no')),
                    'issue' => strip_tags($request->input('issue')),
                    'problem_description' => strip_tags($request->input('problem_description'))
                ]);



            // Logging problem description changes
            if (empty($repair->problem_description) && $request->input('problem_description')) {

                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-1">Problem description changed</span>';
                $log->description = '<strong>"' . strip_tags($request->input('problem_description')) . '"</strong> was added to <strong>problem description</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = '';
                $log->new_value = strip_tags($request->input('problem_description'));
                $log->action = 'added';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();

                $owner->status = 'Problem description of the repair ticket #' . $repair->id . ' has been updated  to ' .  strip_tags($request->input('problem_description')) . '.';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            } else if ($repair->problem_description != $request->input('problem_description') &&  !empty($request->input('problem_description'))) {

                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-1">Problem description changed</span>';
                $log->description = 'Problem Description was updated from <strong>"' . $repair->problem_description . '"</strong> to <strong>"' . strip_tags($request->input('problem_description'))  . '"</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = $repair->problem_description;
                $log->new_value = strip_tags($request->input('problem_description'));
                $log->action = 'added';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();

                $owner->status = 'Problem description of the repair ticket under #' . $repair->id . ' has been updated from ' . $repair->problem_description . ' to ' . strip_tags($request->input('problem_description')) . '.';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            } else if ($repair->problem_description && empty($request->input('problem_description'))) {
                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-1">Problem description changed</span>';
                $log->description = 'Problem Description was removed from <strong>"' . $repair->problem_description . '"</strong> to <strong>""</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = $repair->problem_description;
                $log->new_value = strip_tags($request->input('problem_description'));
                $log->action = 'added';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();

                $owner->status = 'Problem description of the repair ticket under #' . $repair->id . ' has been removed';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            }


            //Update repair status
            if ($repair->product_serial_no != $request->input('serial_no')) {

                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-4">Serial number changed</span>';
                $log->description = '<strong>Serial number</strong> was updated from <strong>' . $repair->product_serial_no . '</strong> to  <strong>' . strip_tags($request->input('serial_no')) . '</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = $repair->product_serial_no;
                $log->new_value = strip_tags($request->input('serial_no'));
                $log->action = 'update';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();

                $owner->status = 'Serial number of the repair ticket under #' . $repair->id . ' has been updated from ' . $repair->product_serial_no . ' to ' . strip_tags($request->input('serial_no')) . '.';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            }

            if ($repair->issue != $request->input('issue')) {

                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-5">Product issue  changed</span>';
                $log->description = '<strong>Product issue</strong> was updated from <strong>' . $repair->issue . '</strong> to  <strong>' . $request->input('issue') . '</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = $repair->issue;
                $log->new_value = strip_tags($request->input('issue'));
                $log->action = 'update';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();

                $owner->status = 'Product issue of the repair ticket under #' . $repair->id . ' has been updated from ' . $repair->issue . ' to ' . strip_tags($request->input('issue')) . '.';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            }

            if ($repair->product != $request->input('product')) {

                $log = new RepairLog;
                $log->repair_id = $id;
                $log->type = '<span class="box-bg bg-color-6">Product changed</span>';
                $log->description = '<strong>Product</strong> was changed from <strong>' . $repair->product . '</strong> to  <strong>' . $request->input('product') . '</strong> by the <strong>customer</strong> with user id <strong>#' . $spg_id . '</strong>';
                $log->old_value = $repair->product;
                $log->new_value = strip_tags($request->input('product'));
                $log->action = 'update';
                $log->user_id = $repair_owner;
                $log->created_by = Auth::user()->id;
                $log->save();


                $owner->status = 'Product of the repair ticket under #' . $repair->id . ' has been updated from ' . $repair->product . ' to ' . strip_tags($request->input('product')) . '.';
                $owner->repair_id = $id;

                event(new RepairStatus($owner));
            }

            session()->flash('alert-success', 'Ticket has been successfully updated!');

            return redirect('repairs/' . $id);
        } else {
            return response()->view('errors.403');
        }
    }



    /**
     * Update repair information (only for SPG internal users)
     * @param  Request $request From the form request
     * @param  int     $id      Unique id of the repair from the repair table
     * @return 
     */
    public function update_repair(Request $request, $id)
    {
        $repair_owner  = RmaTickets::where('id', $id)->value('user_id');
        $owner         = User::find($repair_owner);
        $spg_fname     = Auth::user()->firstname;
        $spg_lname     = Auth::user()->lastname;
        $spg_id        = Auth::user()->id;
        $spg_user_link = url('/profile') . '/' . $spg_id;

        if (optional(Auth::user())->isAdmin() || Auth::user()->id == $repair_owner) {
            $this->validate($request, [
                'requested_date' => 'required',
                'requester_po_number' => 'required',
                'requester_name' => 'required',
                'requester_phone' => 'required',
                'requester_company' => 'required',
                'requester_email' => 'required',
                'company_name' => 'required',
                'country' => 'required',
                'currency' => 'required',
                'company_phone' => 'required',
                'company_address' => 'required'
            ]);

            $repair = RmaTickets::find($id);

            $old_req_name = $repair->requester_name;
            $old_req_phone = $repair->requester_phone;
            $old_req_company = $repair->requester_company;
            $old_req_email = $repair->requester_email;
            $old_po_number = $repair->po_number;
            $old_req_fax = $repair->requester_fax;
            $old_com_address = $repair->company_address;
            $old_com_country = $repair->company_country;
            $old_com_name = $repair->company_name;
            $old_com_fax = $repair->company_fax;
            $old_com_var = $repair->company_isvar;
            $old_currency = $repair->currency;
            $old_com_phone = $repair->company_phone;
            $old_req_date = $repair->requested_date;



            $repair->requested_date = $request->requested_date;
            $repair->po_number = $request->requester_po_number;
            //$repair->status = $request->status;

            $repair->requester_name = $request->requester_name;
            $repair->requester_phone = $request->requester_phone;
            $repair->requester_company = $request->requester_company;
            $repair->requester_email = $request->requester_email;
            $repair->requester_fax = $request->requester_fax;
            $repair->company_name = $request->company_name;
            $repair->company_phone = $request->company_phone;
            $repair->company_fax = $request->company_fax;
            $repair->company_address = $request->company_address;
            $repair->currency = $request->currency;
            $repair->country = $request->country;
            $repair->company_isvar = $request->company_isvar;
            $repair->notify = $request->notify;

            if ($request->status == 'Received') {
                $repair->cust_can_edit = 0;
            }
            $repair->save();


            $log_creator = Auth::user()->firstname . ' ' . Auth::user()->lastname;
            $fieldChanges = array();

            if ($old_req_phone != '' && $old_req_phone != $request->requester_phone) {
                $this->add_rma_log('Requester phone', $old_req_phone, $request->requester_phone, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '4');
                $field = array('field' => 'Phone', 'old' => $old_req_phone, 'new' => $request->requester_phone);
                array_push($fieldChanges, $field);
                //          event( new RepairStatus($owner) );
            }
            if ($old_req_name != '' && $old_req_name != $request->requester_name) {
                $this->add_rma_log('Requester name', $old_req_name, $request->requester_name, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '3');
                $field = array('field' => 'Eequester Name', 'old' => $old_req_name, 'new' => $request->requester_name);
                array_push($fieldChanges, $field);
            }
            if ($old_req_email != '' && $old_req_email != $request->requester_email) {
                $this->add_rma_log('Requester email', $old_req_name, $request->requester_name, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '2');
                $field = array('field' => 'Requester Email', 'old' => $old_req_email, 'new' => $request->requester_email);
                array_push($fieldChanges, $field);
            }
            if ($old_req_company != '' && $old_req_company != $request->requester_company) {
                $this->add_rma_log('Requester company', $old_req_company, $request->requester_company, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '1');
                $field = array('field' => 'Requester Company', 'old' =>  $old_req_company, 'new' => $request->requester_company);
                array_push($fieldChanges, $field);
            }
            if ($old_po_number != '' && $old_po_number != $request->requester_po_number) {
                $this->add_rma_log('PO number', $old_po_number, $request->requester_po_number, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '5');
                $field = array('field' => 'PO Number', 'old' =>  $old_po_number, 'new' =>  $request->requester_po_number);
                array_push($fieldChanges, $field);
            }
            if ($old_currency != '' && $old_currency != $request->currency) {
                $this->add_rma_log('Currency', $old_currency, $request->currency, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '5');
                $field = array('field' => 'Currency', 'old' =>  $old_currency, 'new' => $request->currency);
                array_push($fieldChanges, $field);
            }


            if ($old_com_name != '' && $old_com_name != $request->company_name) {
                $this->add_rma_log('Company name', $old_com_name, $request->company_name, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '2');
                $field = array('field' => 'Company Name', 'old' =>  $old_com_name, 'new' => $request->company_name);
                array_push($fieldChanges, $field);
            }
            if ($old_com_address != '' && $old_com_address != $request->company_address) {
                $this->add_rma_log('Company address', $old_com_address, $request->company_address, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '6');
                $field = array('field' => 'requester_company', 'old' =>  $old_req_company, 'new' => $request->requester_company);
                array_push($fieldChanges, $field);
            }

            if ($old_com_fax != '' && $old_com_fax != $request->company_fax) {
                $this->add_rma_log('Company fax', $old_com_fax, $request->company_fax, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '3');
                $field = array('field' => 'Company Fax', 'old' =>  $old_com_fax, 'new' => $request->company_fax);
                array_push($fieldChanges, $field);
            }

            if ($old_com_country != '' && $old_com_country != $request->country) {
                $this->add_rma_log('Status', $old_com_country, $request->country, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '2');
                $field = array('field' => 'Country', 'old' =>  $old_com_country, 'new' => $request->country);
                array_push($fieldChanges, $field);
            }
            if ($old_com_phone != '' && $old_com_phone != $request->company_phone) {
                $this->add_rma_log('Status', $old_com_phone, $request->company_phone, 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '4');
                $field = array('field' => 'Company Phone', 'old' =>  $old_com_phone, 'new' => $request->company_phone);
                array_push($fieldChanges, $field);
            }

            $emailCotent = new RepairChanges($repair, $fieldChanges);

            //Email if there are any changes
            try {
                Mail::to($this->repair_notification)->send($emailCotent);

                if ($request->notify == 1) {
                    Mail::to($repair->requester_email)->send($emailCotent);
                }
            }
            catch(\Exception $e) {}

            if ($request->status == 'Received') {
                $affected = DB::table('rma_items')->where('rma_id', '=', $id)->update(array('received_date' => date("Y-m-d")));
            }


            session()->flash('alert-success', 'Ticket has been successfully updated!');

            return redirect('repairs/' . $id);
        } else {
            return response()->view('errors.403');
        }
    }


    // Delete RMA STATUS DETAILS
    public function deleteRMAStatus(Request $request)
    {

        $rma_status_id = $request->id;
        $rma_ticket_id = $request->rma_id;

        if ($rma_status_id) {
            $this->createLog(array(
                "field"   => "RMA Status",
                "new_val" => $request->rma_status,
                "action"  => "deleted",
                "rma_id"  => $request->rma_id,
                "color"   => "4",
            ));
            RmaStatus::find($rma_status_id)->destroy($rma_status_id);
            $status = 'RMA status was successfully deleted!';
        } else {
            $status = 'RMA Status ID not found';
        }

        // return redirect('repairs/'. $rma_ticket_id).response()->json([
        //   'success' => true,
        //   'status' => $status
        // ]);

        session()->flash('alert-success', 'RMA Status has been successfully deleted!');

        return response()->json([
            'success' => true,
            'status' => $status
        ]);
    }

    // Delete RMA COMMENTS
    public function deleteRMAComments(Request $request)
    {

        $rma_comment_id = $request->id;
        $rma_id = $request->rma_id;
        $rma_comment = $request->comment;

        if ($rma_comment_id) {

            $this->createLog(
                array(
                    "field"   => "Comment",
                    "new_val" => $rma_comment,
                    "action"  => "deleted",
                    "rma_id"  => $rma_id,
                    "color"   => "4"
                )
            );


            RmaComments::find($rma_comment_id)->destroy($rma_comment_id);
            $comment = 'RMA comment was successfully deleted!';
        } else {
            $comment = 'RMA comment ID not found';
        }

        session()->flash('alert-success', 'Comment has been successfully deleted!');

        return response()->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    public function approveQuotation(Request $request, $id)
    {
        $repair_owner  = RmaTickets::where('id', $id)->value('user_id');
        $repair_company_owner  = RmaTickets::where('id', $id)->value('company_id');
        $owner         = User::find($repair_owner);
        $spg_fname     = Auth::user()->firstname;
        $spg_lname     = Auth::user()->lastname;
        $spg_id        = Auth::user()->id;
        $userID        = $spg_id;
        $spg_user_link = url('/profile') . '/' . $spg_id;

        if (optional(Auth::user())->isAdmin() || Auth::user()->id == $repair_owner ||  $owner->company_id == $repair_company_owner) {
            $repair = RmaTickets::find($id);
            $old_status = $repair->status;
            $repair->status =  $request->selectStatus;

            if ($request->selectStatus == 'Open') {
                $repair->cust_can_edit = 0;
                $rma_status = new RmaStatus;
                $rma_status->status = 'Repair was opened on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                //Mail::to($email)->send(new RepairQuotation($repair));

            } else if ($request->selectStatus == 'Received') {
                $repair->cust_can_edit = 0;
                $rma_status = new RmaStatus;
                $rma_status->status = 'Repair was received on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                try {
                    Mail::to($email)->send(
                        new RepairQuotation(
                            $repair, 
                            "(PACOM) We received your items",
                            $request->selectStatus
                        )
                    );
                }
                catch(\Exception $e) {}
            } else if ($request->selectStatus == 'Shipped') {
                $repair->cust_can_edit = 0;
                $rma_status = new RmaStatus;
                $rma_status->status = 'Repair was shipped on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                try {
                    Mail::to($email)->send(
                        new RepairQuotation(
                            $repair, 
                            "(PACOM) Items Shipped",
                            $request->selectStatus
                        )
                    );
                }
                catch(\Exception $e) {}
            }
             else if ($request->selectStatus == 'To Be Confirmed') {
                $repair->has_quotation = 1;

                $rma_status = new RmaStatus;
                $rma_status->status = 'The client has to confirm this repair. This status was changed on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();


                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                try {
                    Mail::to($email)->send(
                        new RepairQuotation(
                            $repair, 
                            "To be Confirmed - New quotation for R{$repair->id}",
                            $request->selectStatus
                        )
                    );
                }
                catch(\Exception $e) {}
            } else if ($request->selectStatus == 'Confirmed') {
                $repair->has_confirmed = 1;
                $userID = Auth::user()->id;
                $user = User::find(Auth::user()->id);
                $fullname =  $user->firstname . ' ' . $user->lastname;
                $status = $fullname . ' has confirmed the quotation ' . ' on ' . date("Y-m-d");

                $rma_status = new RmaStatus;
                $rma_status->status = $status;
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                session()->flash('alert-success', 'RMA quotation has been successfully confirmed!');
                try {
                    Mail::to($this->repair_notification)->send(
                        new RepairQuotation(
                            $repair, 
                            "Cofirmed - Ticket R{$repair->id}",
                            $request->selectStatus,
                            $this->repair_notification,
                        )
                    );
                }
                catch (\Exception $e) {}
            } else if ($request->selectStatus == 'Submitted') {
                $rma_status = new RmaStatus;
                $rma_status->status = 'This repair was submitted on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                //Mail::to($email)->send(new RepairQuotation($repair));
            } else if ($request->selectStatus == 'Completed') {
                $rma_status = new RmaStatus;
                $rma_status->status = 'Repair completed on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                try {
                    Mail::to($this->repair_notification)->send(
                        new RepairQuotation(
                            $repair, 
                            "Completed - Repair has been Completed for R{$repair->id}",
                            $request->selectStatus,
                            $this->repair_notification,
                        )
                    );
                }
                catch(\Exception $e) {}
            } else if ($request->selectStatus == 'Cancelled') {
                $rma_status = new RmaStatus;
                $rma_status->status = 'This repair was cancelled on ' . date("Y-m-d");
                $rma_status->updated_by = $userID;
                $rma_status->user_id = $userID;
                $rma_status->rma_id = $repair->id;
                $rma_status->save();

                $email = $repair->requester_email;
                session()->flash('alert-success', 'Ticket has been successfully updated!');
                //Mail::to($email)->send(new RepairQuotation($repair));
                try {
                    Mail::to($this->repair_notification)->send(
                        new RepairQuotation(
                            $repair, 
                            "Cancelled - Repair has been Cancelled for R{$repair->id}",
                            $request->selectStatus,
                            $this->repair_notification
                        )
                    );
                }
                catch(\Exception $e) {}
            } else {
                session()->flash('alert-success', 'Ticket has been successfully updated!');
            }
            $repair->save();

            if ($request->selectStatus != 'To Be Confirmed') {
                $repair->id = $repair->id;
                $repair->email = $this->repair_notification;
                $repair->firstname = User::where('email', $this->repair_notification)->value('firstname');
                try {
                    Mail::to($this->repair_notification)->send(
                        new RepairQuotation(
                            $repair,
                            "To be confirmed - New quotation for R{$repair->id}",
                            $request->selectStatus,
                            $this->repair_notification
                        )
                    );
                }
                catch(\Exception $e) {}
            }

            $log_creator = Auth::user()->firstname . ' ' . Auth::user()->lastname;

            $this->add_rma_log('Status', $old_status, 'Confirmed', 'updated',  $log_creator, Auth::user()->id, '', $repair->id, '1');

            if ($request->selectStatus == 'Received') {
                $affected = DB::table('rma_items')->where('rma_id', '=', $id)->update(array('received_date' => date("Y-m-d")));
            }




            return redirect('repairs/' . $id);
        } else {
            return response()->view('errors.quote');
        }
    }

    // Create RMA STATUS DETAILS
    public function updateRMAStatus(Request $request)
    {
        $this->validate($request, [
            'rma_id' => 'required',
            'rma_courier'
        ]);

        $status = $request->rma_status;

        $user = User::find(Auth::user()->id)->get();
        $status = 'Goods have been shipped on ' .  date("Y-m-d");


        // if($request->rma_status === 'Confirmed by' || $request->rma_status === 'Requested by')
        // {
        //   $user = User::find($request->user_id);
        //   $fullname =  $user->firstname . ' ' . $user->lastname;
        //   $status = $request->rma_status . ' ' .  $fullname . ' on ' . $request->rma_date;
        // }
        // else
        // {
        //   $user = User::find(Auth::user()->id)->get();
        //   $status = $request->rma_status . ' ' .  $request->rma_date;
        // }

        $rma_status = new RmaStatus;
        $rma_status->status = $status;
        $rma_status->updated_by = Auth::user()->id;
        $rma_status->user_id = Auth::user()->id;
        $rma_status->rma_id = $request->rma_id;
        $rma_status->created_at = date("Y-m-d");
        $rma_status->courier = $request->rma_courier;
        $rma_status->consignment_note = $request->consignment_note;
        $rma_status->save();

        $log_creator = Auth::user()->firstname . ' ' . Auth::user()->lastname;

        $updateRMAStatus = RmaTickets::find($request->rma_id);
        $updateRMAStatus->status = 'Shipped';
        $updateRMAStatus->save();

        if ($request->rma_status) {
            $this->add_rma_log('RMA Status', '', $request->rma_status, 'updated',  $log_creator, Auth::user()->id, '', $request->rma_id, '1');

            $repair_owner = RmaTickets::find($request->rma_id);

            // if($request->rma_status == 'To be confirmed on') {
            //   $request->session()->flash('alert-success', 'Ticket has been successfully updated!');
            //   Mail::to($repair_owner->requester_email)->send(new RepairQuotation($repair_owner));
            // }
            // else {
            //   $field = array('status' => $status,'rma_id' =>  $request->rma_id, 'date' =>  $request->rma_date, 'fullname' => $repair_owner->requester_name, 'courier' => $request->courier,'consignment_note' => $request->consignment_note  );
            //   Mail::to($repair_owner->requester_email)->send(new RepairRmaStatus($field));
            // }

        }

        session()->flash('alert-success', 'Status has been successfully added!');

        return redirect('repairs/' . $request->rma_id);
    }


    // 
    private function add_rma_log($field, $old_val, $new_val, $action, $created_by, $user_id, $rma_item_id, $rma_id, $color = '4')
    {
        $log = new RmaLogs;

        if ($rma_id == '' &&  $rma_item_id != '') {
            $log->rma_id = $rma_item_id;
        } else {
            $log->rma_id = $rma_id;
        }

        if ($old_val != '' && $old_val != $new_val) {
            $type_stat = 'updated';
        } else {
            $type_stat = 'added';
        }

        if ($action == 'deleted') {
            $type_stat = 'deleted';
        }

        $log->type = '<span class="box-bg bg-color-' . $color . '">' . $field . ' ' . $type_stat . '</span>';
        $log->description = '<strong>' . $field . '</strong> was ' .  $type_stat . ($old_val ? ' from <strong>' . $old_val . '</strong> to ' : "") . ' <strong> ' . strip_tags($new_val) . '</strong> by <strong>#' . $created_by . '</strong>';
        $log->old_value = $old_val;
        $log->new_value = $new_val;
        $log->action = $action;
        $log->user_id = $user_id;
        $log->created_by = $created_by;
        $log->save();

        //  if($type_stat == 'updated') {
        //     $owner->status = $field . ' of the RMA #' . $rma_id . ' has been updated from ' . $old_val . ' to ' . $new_val . '.';

        //     $owner->repair_id = $rma_id;
        //     event( new RepairStatus($owner) );
        //  } 
    }

    public function addItemRMA(Request $request)
    {
        if ($request->ajax()) {

            if (Auth::user()) {

                $rma_id = $request->rma_id;
                $serial_number = $request->serial_number;
                $model = $request->model;
                $repair_cost = $request->repair_cost;
                $original_order_date = $request->original_order_date;
                $date_purchased = $request->date_purchase_known;
                $invalid_serial_number = $request->invalid_serial_number;
                $warranty_flag = $request->warranty_flag;
                $fault_comment = $request->fault_comment;


                //saving data
                $rmaItem = new RmaItems;
                $rmaItem->serial_number = $serial_number;
                $rmaItem->model = $model;
                $rmaItem->repair_cost =  $repair_cost;
                $rmaItem->date_purchased =  $date_purchased;
                $rmaItem->invalid_serial_number =  $invalid_serial_number;
                // if($warranty_flag != ""  && $warranty_flag != null && $warranty_flag != "0" && $warranty_flag != 0) {
                //   $rmaItem->under_warranty =  $warranty_flag;
                // }
                if ($warranty_flag != ""  && $warranty_flag != null) {
                    $rmaItem->under_warranty =  $warranty_flag;
                }
                $rmaItem->original_order_date = $original_order_date;
                $rmaItem->rma_id = $rma_id;
                $rmaItem->fault_described_by_customer = $fault_comment;

                $rmaItem->save();

                if ($request->faults) {
                    foreach ($request->faults as $fault) {
                        $faultItem = new RmaItemFaults;
                        $faultItem->fault = $fault;
                        $faultItem->rma_items_id = $rmaItem->id;
                        $faultItem->save();
                    }
                }

                $rmaItem->faults = $request->faults;


                $this->createLog(array(
                    "field"   => "Fault Item",
                    "action"  => "added",
                    "rma_id"  => $rma_id,
                    "cus_desc" => 'New Fault item <strong>#' . $serial_number . '</strong> was <strong>added</strong> '
                ));

                $rma_ticket = RmaTickets::find($rma_id);

                try {
                    if ($request->isCustomer ==  'yes') {
                        $requester_email = RmaTickets::where('id', $rma_id)->value('requester_email');
                        Mail::to($this->repair_notification)->send(new RepairItemAdded($rmaItem));
                    } else {
                        $requester_email = RmaTickets::where('id', $rma_id)->value('requester_email');
                        //Mail::to($requester_email)->send(new RepairItemAdded($rmaItem));
                    }

                    $this->sendNotifiedRequesterEmail($rma_id, function () use ($rma_ticket, $rmaItem) {
                        Mail::to($rma_ticket->requester_email)->send(new RepairItemAdded($rmaItem));
                    });
                }
                catch (\Exception $e) {}

                session()->flash('alert-success', 'Fault item has been successfully added!');

                return response()->json([
                    'success' => true,
                    'item' => $rmaItem,
                    'faults' => $rmaItem->faults
                ]);
            }
        }
    }

    public function update_RMA_item(Request $request, $rma_id)
    {

        $oldItem = RmaItems::find($rma_id);
        $oldFaultItems = $oldItem->faults;

        //Store old values
        $old_rma_id_input = $oldItem->rma_id;
        $old_serial_number = $oldItem->serial_number;
        $old_model = $oldItem->model;
        $old_repair_cost = $oldItem->repair_cost;
        $old_original_order_date = $oldItem->original_order_date;
        $old_date_purchased = $oldItem->date_purchased;
        $old_invalid_serial_number = $oldItem->invalid_serial_number;
        $old_under_warranty = $oldItem->under_warranty;
        $old_fault_comment = $oldItem->fault_described_by_customer;
        $old_rma_status = $oldItem->status;

        $old_root_cause_analysis = $oldItem->root_cause_analysis;
        $old_pacom_fault_description = $oldItem->pacom_fault_description;
        $old_pacom_comment = $oldItem->pacom_comment;

        $old_received_date = $oldItem->received_date;
        $old_repaired_date = $oldItem->repaired_date;


        //New values from ajax
        $rma_id_input = $request->rma_id;
        $serial_number = $request->serial_number;
        $model = $request->model;
        $repair_cost = $request->repair_cost;
        $original_order_date = $request->original_order_date;
        $date_purchased = $request->date_purchased;
        $invalid_serial_number = $request->invalid_serial_number;
        $under_warranty = $request->under_warranty;
        $fault_comment = $request->fault_comment;
        $status = $request->status;

        $root_cause_analysis = $request->root_cause_analysis;
        $pacom_fault_description = $request->pacom_fault_description;
        $pacom_comment = $request->pacom_comment;

        $received_date = $request->received_date;
        $repaired_date = $request->repaired_date;

        $fieldChanges = array();

        if ($old_serial_number != $serial_number) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $serial_number,
                    "old_val" => $old_serial_number,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $old_serial_number . '</strong> was updated into <strong>#' . $serial_number . '</strong> '
                ));

                $field = array('field' => 'Serial Number', 'old' =>  $old_serial_number, 'new' => $serial_number);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_model != $model) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $model,
                    "old_val" => $old_model,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Model was updated from <strong>' . $old_model . '</strong> to <strong>' . $model . '</strong> '
                ));

                $field = array('field' => 'Model', 'old' =>  $old_model, 'new' => $model);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_repair_cost != $repair_cost) {
            if ($rma_id_input) {
                if (!$old_repair_cost) {
                    $string = ' <strong>added</strong> ' . $repair_cost . '<strong> ';
                } else if (!$repair_cost) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_repair_cost . '</strong> to <strong>' . $repair_cost . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $repair_cost,
                    "old_val" => $old_repair_cost,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Repair Cost was' . $string
                ));

                $field = array('field' => 'Repair Cost', 'old' =>  $old_repair_cost, 'new' => $repair_cost);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_original_order_date != $original_order_date) {
            if ($rma_id_input) {
                if (!$old_original_order_date) {
                    $string = ' <strong>added</strong> ' . $original_order_date . '<strong> ';
                } else if (!$original_order_date) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_original_order_date . '</strong> to <strong>' . $original_order_date . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_original_order_date,
                    "old_val" => $original_order_date,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Original Order Date was' . $string
                ));

                $field = array('field' => 'Original Order Date ', 'old' =>  $old_original_order_date, 'new' => $original_order_date);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_date_purchased != $date_purchased) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_date_purchased,
                    "old_val" => $date_purchased,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Date Purchase Known was updated from <strong> ' . ($old_date_purchased === 1 ? 'Yes' : 'No') . '</strong> to <strong>' . ($date_purchased === 1 ? 'Yes' : 'No') . '</strong> '
                ));

                if ($old_date_purchased == null || $old_date_purchased == 'null') {
                    $old_dp = 'N/A';
                } elseif ($old_date_purchased == 0) {
                    $old_dp = 'No';
                } elseif ($old_date_purchased == 1) {
                    $old_dp = 'Yes';
                } else {
                    $old_dp = 'N/A';
                }

                if ($date_purchased == 1) {
                    $new_dp = 'Yes';
                } else {
                    $new_dp = 'No';
                }

                $field = array('field' => 'Date Purchase Known', 'old' =>  $old_dp, 'new' => $new_dp);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_invalid_serial_number != $invalid_serial_number) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_invalid_serial_number,
                    "old_val" => $invalid_serial_number,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Invalid Serial Number was updated from <strong> ' . ($old_invalid_serial_number === 1 ? 'Yes' : 'No') . '</strong> to <strong>' . ($invalid_serial_number === 1 ? 'Yes' : 'No') . '</strong> '
                ));

                if ($old_invalid_serial_number == null || $old_invalid_serial_number == 'null') {
                    $old_sn = 'N/A';
                } elseif ($old_invalid_serial_number == 0) {
                    $old_sn = 'No';
                } elseif ($old_invalid_serial_number == 1) {
                    $old_sn = 'Yes';
                } else {
                    $old_sn = 'N/A';
                }

                if ($invalid_serial_number == 1) {
                    $new_sn = 'Yes';
                } else {
                    $new_sn = 'No';
                }


                $field = array('field' => 'Invalid Serial Number', 'old' =>  $old_sn, 'new' => $new_sn);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_under_warranty != $under_warranty) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_under_warranty,
                    "old_val" => $under_warranty,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Under Warranty was updated from <strong> ' . ($old_under_warranty === 1 ? 'Yes' : 'No') . '</strong> to <strong>' . ($under_warranty === 1 ? 'Yes' : 'No') . '</strong> '
                ));

                if ($old_under_warranty == null || $old_under_warranty == 'null') {
                    $old_uw = 'N/A';
                } elseif ($old_under_warranty == 0) {
                    $old_uw = 'No';
                } elseif ($old_under_warranty == 1) {
                    $old_uw = 'Yes';
                } else {
                    $old_uw = 'N/A';
                }

                if ($under_warranty == 1) {
                    $new_uw = 'Yes';
                } else {
                    $new_uw = 'No';
                }

                $field = array('field' => 'Under Warranty', 'old' =>  $old_uw, 'new' => $new_uw);
                array_push($fieldChanges, $field);
            }
        }

        if ($old_rma_status != $status) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_rma_status,
                    "old_val" => $status,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Under Warranty was updated from <strong> ' . ($old_rma_status === 1 ? 'Yes' : 'No') . '</strong> to <strong>' . ($status === 1 ? 'Yes' : 'No') . '</strong> '
                ));

                $field = array('field' => 'Status', 'old' =>  $old_rma_status, 'new' => $status);
                array_push($fieldChanges, $field);
            }
        }

        if ($old_fault_comment != $fault_comment) {
            if ($rma_id_input) {
                if (!$old_fault_comment) {
                    $string = ' <strong>added</strong> ' . $fault_comment . '<strong> ';
                } else if (!$fault_comment) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_fault_comment . '</strong> to <strong>' . $fault_comment . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_fault_comment,
                    "old_val" => $fault_comment,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> fault comment was' . $string
                ));

                $field = array('field' => 'Fault Comment', 'old' =>  $old_fault_comment, 'new' => $fault_comment);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_root_cause_analysis != $root_cause_analysis) {
            if ($rma_id_input) {
                if (!$old_root_cause_analysis) {
                    $string = ' <strong>added</strong> ' . $root_cause_analysis . '<strong> ';
                } else if (!$root_cause_analysis) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_root_cause_analysis . '</strong> to <strong>' . $root_cause_analysis . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_root_cause_analysis,
                    "old_val" => $root_cause_analysis,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Root Cause Analysis was' . $string
                ));

                $field = array('field' => 'Root Cause Analysis', 'old' =>  $old_root_cause_analysis, 'new' => $root_cause_analysis);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_pacom_fault_description != $pacom_fault_description) {
            if ($rma_id_input) {
                if (!$old_pacom_fault_description) {
                    $string = ' <strong>added</strong> ' . $pacom_fault_description . '<strong> ';
                } else if (!$pacom_fault_description) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_pacom_fault_description . '</strong> to <strong>' . $pacom_fault_description . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_pacom_fault_description,
                    "old_val" => $pacom_fault_description,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Pacom Fault Description was' . $string
                ));

                $field = array('field' => 'Pacom Fault Description', 'old' =>  $old_pacom_fault_description, 'new' => $pacom_fault_description);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_pacom_comment != $pacom_comment) {
            if ($rma_id_input) {
                if (!$old_pacom_comment) {
                    $string = ' <strong>added</strong> ' . $pacom_comment . '<strong> ';
                } else if (!$pacom_comment) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_pacom_comment . '</strong> to <strong>' . $pacom_comment . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_pacom_comment,
                    "old_val" => $pacom_comment,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Pacom Comment was' . $string
                ));

                $field = array('field' => 'Pacom Comment', 'old' =>  $old_pacom_comment, 'new' => $pacom_comment);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_received_date != $received_date) {

            if ($rma_id_input) {
                if (!$old_received_date) {
                    $string = ' <strong>added</strong> ' . $received_date . '<strong> ';
                } else if (!$received_date) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_received_date . '</strong> to <strong>' . $received_date . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_received_date,
                    "old_val" => $received_date,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Received Date was' . $string
                ));

                $field = array('field' => 'Received Date', 'old' =>  $old_received_date, 'new' => $received_date);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_repaired_date != $repaired_date) {
            if ($rma_id_input) {
                if (!$old_repaired_date) {
                    $string = ' <strong>added</strong> ' . $repaired_date . '<strong> ';
                } else if (!$repaired_date) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_repaired_date . '</strong> to <strong>' . $repaired_date . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_repaired_date,
                    "old_val" => $repaired_date,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Repaired Date was' . $string
                ));

                $field = array('field' => 'Repaired Date', 'old' =>  $old_repaired_date, 'new' => $repaired_date);
                array_push($fieldChanges, $field);
            }
        }



        if ($oldFaultItems != $request->faults) {
            $newItems = $request->faults;
            $oldFaultsString = "";
            foreach (json_decode($oldFaultItems) as $key => $oldFault) {
                $oldFaultsString .= ($key === 0 ? "" : ", ") . $oldFault->fault;
            }
            $newFaultsString = "";
            foreach (json_decode($newItems) as $key => $newFault) {
                $newFaultsString .= ($key === 0 ? "" : ", ") . $newFault->fault;
            }
            $oldItemTotal = count($oldFaultItems);
            $newItemTotal = count(json_decode($newItems));
            $count = 0;
            foreach (json_decode($oldFaultItems) as $key => $oldFault) {

                foreach (json_decode($newItems) as $key => $newFault) {
                    $count++;
                    // 1 = 5, 1 = 4, 1 = 3, 1 = 1, 1 = 2
                    if ($oldFault->fault == $newFault->fault) {
                        $hasChanges = false;
                        break;
                    } else {
                        $hasChanges = true;
                        //log
                    }
                    if ($count == $newItemTotal && $hasChanges) {
                        $string = ' <strong>updated</strong> from <strong> ' . $oldFaultsString . '</strong> to <strong>' . $newFaultsString . '</strong> ';
                        $this->createLog(array(
                            "field"   => "Fault Item",
                            "new_val" => $oldFault->fault,
                            "old_val" => $newFault->fault,
                            "action"  => "updated",
                            "rma_id"  => $rma_id_input,
                            "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Fault Category was' . $string
                        ));

                        $field = array('field' => 'Fault Category', 'old' =>  '', 'new' => '');
                        array_push($fieldChanges, $field);
                    }
                }
            }
        }

        if ($request) {
            DB::table('rma_items')->where('id', $rma_id)->update([
                'serial_number' => $serial_number,
                'model' => $model,
                'repair_cost' => $repair_cost,
                'original_order_date' => $original_order_date,
                'date_purchased' => $date_purchased,
                'invalid_serial_number' => $invalid_serial_number,
                'under_warranty' => $under_warranty,
                'fault_described_by_customer' => $fault_comment,
                'root_cause_analysis' => $root_cause_analysis,
                'pacom_fault_description' => $pacom_fault_description,
                'pacom_comment' => $pacom_comment,
                'status' => $status,
                'received_date' => $received_date,
                'repaired_date' => $repaired_date
            ]);
        }



        if ($request->faults) {
            $faults = json_decode($request->faults, true);
            DB::table('rma_item_faults')->where('rma_items_id', $rma_id)->delete();
            DB::table('rma_item_faults')->insert($faults);
        }

        $item = RmaItems::findorfail($rma_id);

        $item->faults = $item->faults;

        $requester_name = RmaTickets::where('id', $rma_id_input)->value('requester_name');
        $requester_email = RmaTickets::where('id', $rma_id_input)->value('requester_email');

        $emailContent = new RepairItemChanges($rma_id_input, $fieldChanges, $requester_name);

        try {
            $this->sendNotifiedRequesterEmail($request->rma_id, function () use ($requester_email, $emailContent) {
                Mail::to($requester_email)->send($emailContent);
            });
        }
        catch(\Exception $e) {}

        session()->flash('alert-success', 'Fault item has been successfully updated!');

        return response()->json([
            'success' => true,
            'item' => $item,
            'faults' => $request->faults
        ]);
    }

    public function updateItemByCust(Request $request, $rma_id)
    {

        $oldItem = RmaItems::find($rma_id);
        // $repair_faults = DB::table('rma_item_faults')->find($rma_id);
        $oldFaultItems = $oldItem->faults;


        //Store old values
        $old_rma_id_input = $oldItem->rma_id;
        $old_serial_number = $oldItem->serial_number;
        $old_model = $oldItem->model;
        $old_date_purchased = $oldItem->date_purchased;
        $old_invalid_serial_number = $oldItem->invalid_serial_number;
        $old_fault_comment = $oldItem->fault_described_by_customer;


        //New values from ajax
        $rma_id_input = $request->rma_id;
        $serial_number = $request->serial_number;
        $model = $request->model;
        $fault_comment = $request->fault_comment;

        $fieldChanges = array();

        if ($old_serial_number != $serial_number) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $serial_number,
                    "old_val" => $old_serial_number,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $old_serial_number . '</strong> was updated into <strong>#' . $serial_number . '</strong> '
                ));
                $field = array('field' => 'Serial Number', 'old' =>  $old_serial_number, 'new' => $serial_number);
                array_push($fieldChanges, $field);
            }
        }
        if ($old_model != $model) {
            if ($rma_id_input) {
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $model,
                    "old_val" => $old_model,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Model was updated from <strong>' . $old_model . '</strong> to <strong>' . $model . '</strong> '
                ));
                $field = array('field' => 'Model', 'old' =>  $old_model, 'new' => $model);
                array_push($fieldChanges, $field);
            }
        }

        // if ($old_date_purchased != $date_purchased){
        //   if($rma_id_input){
        //     $this->createLog(array(
        //       "field"   => "Fault Item",
        //       "new_val" => $old_date_purchased,
        //       "old_val" => $date_purchased,
        //       "action"  => "updated",
        //       "rma_id"  => $rma_id_input,
        //       "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Date Purchase Known was updated from <strong> ' . ($old_date_purchased === 1 ? 'Yes' : 'No' ) .'</strong> to <strong>' . ($date_purchased === 1 ? 'Yes' : 'No' ) .'</strong> '
        //     ));
        //       if($old_date_purchased == null || $old_date_purchased == 'null') {
        //           $old_dp = 'N/A';
        //       }
        //       elseif($old_date_purchased == 0) {
        //         $old_dp = 'No';
        //       } 
        //       elseif($old_date_purchased == 1) {
        //         $old_dp = 'Yes';
        //       } else {
        //         $old_dp = 'N/A';
        //       } 

        //       if($date_purchased == 1) {
        //         $new_dp = 'Yes';
        //       }
        //       else {
        //         $new_dp = 'No';
        //       }

        //       $field = array('field' => 'Date Purchase Known','old' =>  $old_dp, 'new' => $new_dp);
        //       array_push($fieldChanges, $field);

        //   }
        // }

        if ($old_fault_comment != $fault_comment) {
            if ($rma_id_input) {
                if (!$old_fault_comment) {
                    $string = ' <strong>added</strong> ' . $fault_comment . '<strong> ';
                } else if (!$fault_comment) {
                    $string = ' <strong>removed</strong> ';
                } else {
                    $string = ' <strong>updated</strong> from <strong> ' . $old_fault_comment . '</strong> to <strong>' . $fault_comment . '</strong> ';
                }
                $this->createLog(array(
                    "field"   => "Fault Item",
                    "new_val" => $old_fault_comment,
                    "old_val" => $fault_comment,
                    "action"  => "updated",
                    "rma_id"  => $rma_id_input,
                    "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> fault comment was' . $string
                ));

                $field = array('field' => 'Fault Comment', 'old' =>  $old_fault_comment, 'new' => $fault_comment);
                array_push($fieldChanges, $field);
            }
        }

        if ($oldFaultItems != $request->faults) {
            $newItems = $request->faults;
            $oldFaultsString = "";
            foreach (json_decode($oldFaultItems) as $key => $oldFault) {
                $oldFaultsString .= ($key === 0 ? "" : ", ") . $oldFault->fault;
            }
            $newFaultsString = "";
            foreach (json_decode($newItems) as $key => $newFault) {
                $newFaultsString .= ($key === 0 ? "" : ", ") . $newFault->fault;
            }
            $oldItemTotal = count($oldFaultItems);
            $newItemTotal = count(json_decode($newItems));
            $count = 0;
            foreach (json_decode($oldFaultItems) as $key => $oldFault) {

                foreach (json_decode($newItems) as $key => $newFault) {
                    $count++;
                    // 1 = 5, 1 = 4, 1 = 3, 1 = 1, 1 = 2
                    if ($oldFault->fault == $newFault->fault) {
                        $hasChanges = false;
                        break;
                    } else {
                        $hasChanges = true;
                    }
                    if ($count == $newItemTotal && $hasChanges) {
                        $string = ' <strong>updated</strong> from <strong> ' . $oldFaultsString . '</strong> to <strong>' . $newFaultsString . '</strong> ';
                        $this->createLog(array(
                            "field"   => "Fault Item",
                            "new_val" => $oldFault->fault,
                            "old_val" => $newFault->fault,
                            "action"  => "updated",
                            "rma_id"  => $rma_id_input,
                            "cus_desc" => 'Fault item <strong>#' . $serial_number . '</strong> Fault Category was' . $string
                        ));

                        $field = array('field' => 'Fault Category', 'old' =>  '', 'new' => '');
                        array_push($fieldChanges, $field);
                    }
                }
            }
        }


        if ($request) {
            DB::table('rma_items')->where('id', $rma_id)->update([
                'serial_number' => $serial_number,
                'model' => $model,
                // 'date_purchased' => $date_purchased,
                // 'original_order_date' => $original_order_date,
                'fault_described_by_customer' => $fault_comment,

            ]);
        }

        if ($request->faults) {
            $faults = json_decode($request->faults, true);
            DB::table('rma_item_faults')->where('rma_items_id', $rma_id)->delete();
            DB::table('rma_item_faults')->insert($faults);
        }

        $item = RmaItems::findorfail($rma_id);

        $item->faults = $item->faults;
        $requester_name = RmaTickets::where('id', $rma_id_input)->value('requester_name');
        $requester_email = RmaTickets::where('id', $rma_id_input)->value('requester_email');
        
        try {
            Mail::to($this->repair_notification)->send(new RepairItemChanges($rma_id_input, $fieldChanges, 'Admin'));
        }
        catch(\Exception $e) {}

        session()->flash('alert-success', 'Fault item has been successfully updated!');

        return response()->json([
            'success' => true,
            'item' => $item,
            'faults' => $request->faults
        ]);
    }

    public function delete_rma(Request $request)
    {

        $id = $request->id;
        $serial = $request->serial;
        $rma_id = $request->rma_id;

        if ($rma_id) {
            $this->createLog(array(
                "field"   => "Fault Item",
                "action"  => "deleted",
                "rma_id"  => $rma_id,
                "cus_desc" => ' Fault item <strong>#' . $serial . '</strong> was <strong>deleted</strong> '
            ));
            $faulty_item = RmaItems::find($id);
            $requester_email = RmaTickets::where('id', $rma_id)->value('requester_email');
            //Mail::to($requester_email)->send(new RepairItemDeleted($faulty_item));

            try {
                $this->sendNotifiedRequesterEmail($request->rma_id, function () use ($requester_email, $faulty_item) {
                    Mail::to($requester_email)->send(new RepairItemDeleted($faulty_item));
                });
            }
            catch (\Exception $e) {}

            RmaItems::destroy($id);

            $status = 'RMA was successfully deleted!';
            $success = true;
        } else {
            $status = 'RMA ID not found';
            $success = false;
        }

        session()->flash('alert-success', 'Fault item has been successfully deleted!');
        return response()->json([
            'status' => $status,
            'success' => $success
        ]);
    }

    #Create log from any activity in repair page.
    private function createLog($actLog)
    {
        $log          = new RmaLogs;
        $created_by   = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $user_id      = Auth::user()->id;
        $field        = isset($actLog["field"]) ? $actLog["field"] : "";
        $old_val      = isset($actLog["old_val"]) ? $actLog["old_val"] : "";
        $new_val      = isset($actLog["new_val"]) ? $actLog["new_val"] : "";
        $action       = isset($actLog["action"]) ? $actLog["action"] : "";
        $rma_item_id  = isset($actLog["rma_item_id"]) ? $actLog["rma_item_id"] : "";
        $rma_id       = isset($actLog["rma_id"]) ? $actLog["rma_id"] : null;
        $cus_desc     = isset($actLog["cus_desc"]) ? $actLog["cus_desc"] : "";
        $new_item_val = isset($actLog["new_item_val"]) ? $actLog["new_item_val"] : "";


        if (isset($actLog["color"])) {
            $color = $actLog["color"];
        } else if ($action === "added") {
            $color = "3";
        } else if ($action === "updated") {
            $color = "1";
        } else if ($action === "deleted") {
            $color = "4";
        } else {
            $color = "5";
        }


        if ($rma_id == '' &&  $rma_item_id != '') {
            $log->rma_id = $rma_item_id;
        } else {
            $log->rma_id = $rma_id;
        }

        # Shorten text.
        // $trimText = "";
        // if (strlen($new_val) > 15) // trim text max of 15 chars
        // {
        //     $maxLength = 14;
        //     $text = substr($new_val, 0, $maxLength);
        //     $trimText = $text  . ' ... ';
        // } else {
        //     $trimText = $new_val;
        // }

        $defaultDesc = $field . ' was ' .  $action . ($old_val ? ' from <strong>' . $old_val . '</strong> to ' : "") . ' <strong> ' . strip_tags($new_val) . '</strong> by <strong>#' . $created_by . '</strong>';

        $log->type = '<span class="box-bg bg-color-' . $color . '">' .  $field . ' ' .  $action . '</span>';

        $log->description =  $cus_desc ? $cus_desc . 'by <strong>#' . $created_by . '</strong>' : $defaultDesc;

        $log->old_value   = $old_val;
        $log->new_value   = $new_val;
        $log->action      = $action;
        $log->user_id     = $user_id;
        $log->created_by  = $created_by;
        $log->save();
    }

    public function setUserNotification(Request $request)
    {
        $status = $request->input('status');
        $rma_id = $request->input('rma_id');

        if ($rma_ticket = RmaTickets::find($rma_id)) {
            $rma_ticket->notify = $status;
            $rma_ticket->save();

            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => false
        ]);
    }
}
