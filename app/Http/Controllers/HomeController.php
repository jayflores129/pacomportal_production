<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Files;
use App\Models\Category;
use App\Models\Documents;
use DB;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Repairs;
use App\Models\Login;
use App\Models\Releases;
use App\Models\RepairLog;
use App\Models\RmaTickets;
use App\Models\Software;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Microsoft\Graph\Connect\Constants;
use Auth;



class HomeController extends Controller
{
    public $categories;
    protected $client;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->categories = Category::all();

        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

      $files = Files::orderBy('created_at', 'DESC')->take(3)->get();
      $logs  = $this->login_logs();
      $id    = Auth::user()->id;

      if( Auth::user()->isAdmin() ) {
          $logs = RmaTickets::take(50)
                    ->orderBy('created_at', 'DESC')  
                    ->get();
      } else {
        $logs = RmaTickets::take(50)
                    ->where(function( $query ) use ( $id){
                            $query->where('user_id', $id);
                     })
                    ->orderBy('created_at', 'DESC')  
                    ->get();
      }

      if( Auth::user()->isAdmin() ) {
        $tasks = Software::where(function($query) {
                              $query->where('resolve', '=', NULL)
                                    ->orWhere('resolve', '=', 0);
                          })
                  ->orderBy('updated_at', 'DESC')
                  ->take(10)->get();
      } else {
        $user_company = Auth::user()->company;
        $tasks = Software::where( 'company', $user_company )
                          ->where(function($query) {
                              $query->where('resolve', '=', NULL)
                                    ->orWhere('resolve', '=', 0);
                          })
                         ->orderBy('updated_at', 'DESC')->take(10)->get();
      }

                       

      return view('home')->with([
            'files' => $files, 
            'open_repairs' =>  $this->open_repairs(),  
            'repaired_products' =>  $this->repaired_products(),  
            'returned_products' =>  $this->returned_products(), 
            'partially_shipped' =>  $this->partially_shipped(), 
            'completely_shipped' =>  $this->completely_shipped(), 
            'recent_tickets' =>  $this->company_tickets(),
            'user_logs' =>  $logs,
            'login_history' =>  $this->login_history(),
            'open_products' => $this->open_products(),
            'topTasks' => $this->topTasks(),
            'topDownloads' => $this->topDownloads(),
            'tickets' => $logs,
            'tasks' => $tasks  

        ]);
    }

    private function login_logs() {
      $db = DB::table('users')->get();
      return $db;               
    }

    public function open_repairs() {
      if( $this->isAdminRole() ) {
         return DB::table('repairs')->where('status', 'open')->count();
      } else {
        return DB::table('repairs')
          ->where('status', 'open')
          ->where(function($query){
              $query->where('user_id', Auth::user()->id);
          })
          ->count();
      }

    }

    public function repaired_products() {
      if( $this->isAdminRole() ) {
        return DB::table('repairs')->where('status', 'repaired')->count();
      } else {
        return DB::table('repairs')
          ->where('status', 'repaired')
          ->where(function($query){
              $query->where('user_id', Auth::user()->id);
          })
          ->count();
      }
    }

    public function returned_products() {

      if( $this->isAdminRole() ) {
        return DB::table('repairs')->where('status', 'returned')->count();
      } else {
        return DB::table('repairs')
          ->where('status', 'returned')
          ->where(function($query){
              $query->where('user_id', Auth::user()->id);
          })
          ->count();
      }
    }

    public function partially_shipped() {
      if( $this->isAdminRole() ) {

        return DB::table('repairs')->where('status', 'Partially Shipped')->count();

      } else {
        return DB::table('repairs')
          ->where('status', 'Partially Shipped')
          ->where(function($query){
              $query->where('user_id', Auth::user()->id);
          })
          ->count();
      }
    }


    public function completely_shipped() {
      if( $this->isAdminRole() ) {
        return DB::table('repairs')->where('status', 'Completely Shipped')->count();
      } else {
        return DB::table('repairs')
          ->where('status', 'Completely Shipped')
          ->where(function($query){
              $query->where('user_id', Auth::user()->id);
          })
          ->count();
      }

    }


    public function company_tickets() {
       return DB::table('repairs')
                  ->where('status', 'open')
                  ->orderby('created_at', 'desc')
                  ->take(10)
                  ->get();

    }

    /**
     * Check admin role
     * @return boolean [description]
     */
    public function isAdminRole() {
        if( Auth::user()->isAdmin() ) {
           return true;
        }
        return false;
    } 
    
    /**
     * Show top products from open tickets
     * @return [type] [description]
     */
    public function open_products() {

      $products = DB::table('products')->get();
      $temp_array = array();

      foreach( $products as $product ) {
        $product_name = $product->name; 
         
        if( Auth::user()->isAdmin() ) {

            $total_prod = Repairs::where('status',  'open')
                          ->where(function($query) use ( $product_name ) {
                              $query->where('product', '=', $product_name);
                          })
                          ->count();

            $temp_array[$product_name] = $total_prod;  

        } else {
            $total_prod = Repairs::where('status',  'open')
                          ->where('user_id', Auth::user()->id)
                          ->where(function($query) use ( $product_name ) {
                              $query->where('product', '=', $product_name);
                          })
                          ->count();

            $temp_array[$product_name] = $total_prod;

        }
           
      }
        return json_encode($temp_array);
    }


    /**
     * Show top products from open tickets
     * @return [type] [description]
     */
    public function topTasks() {

      $status = ['To Do','In Progress','Completed'];

      $temp_array = array();

      foreach( $status as $status ) {
        $name = $status;

        if( Auth::user()->isAdmin() ) {
            $total = Software::where('status',  $status)
                          ->where(function($query) {
                              $query->where('resolve', '=', NULL)
                                    ->orWhere('resolve', '=', 0);
                          })
                          ->count();

            $temp_array[$name] = $total;  

        } else {
           $user_company = Auth::user()->company;
            $total = Software::where('status',  $status)
                          ->where(function($query) use ( $user_company ){
                              $query->where('company', $user_company);
                          })
                          ->where(function($query) {
                              $query->where('resolve', '=', NULL)
                                    ->orWhere('resolve', '=', 0);
                          })
                          ->count();

            $temp_array[$name] = $total; 

        }
           
      }
        return json_encode($temp_array);
    }


    public function topDownloads()
    {
          $files = DB::table('files')->orderBy('downloads', 'DESC')->take(3)->get();   
          return $files;
    }


    /**
     * Retreive login history
     * @return [type] [description]
     */
    public function login_history() {

      return DB::table('login_history')->get();
    }



    public function thank_you() 
    {
       return view('thank-you'); 
    }


    public function connect_api() 
    {
       $token = session('onedrive_accessToken');
       $token_expires = session('token_expires');

       return view('api/connect-api')->with(['token' => $token, 'token_expires' => $token_expires]); 
    }

  }



 