<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Repairs;
use Auth;
use DB;
use URL;
use App\Models\Search;
use App\Models\SearchDefault;
use App\Models\Company;
use App\Models\RmaTickets;
use App\Models\Issues;
use App\Models\RmaItemFaults;
use App\Models\RmaItems;
use App\Models\ItemStatus;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SearchController extends Controller
{
	public $issues;
  	public $products;
  	private $userID;
	private $id;

	public function __construct()
	{
	
		$this->issues   = DB::table('issues')->get();
        $this->products = DB::table('products')->get();
        $this->users = DB::table('users')->get();
        $this->companies = DB::table('companies')->get();
	}	
    

    public function search_rma() {
        $itemstatus = ItemStatus::all();
        $userID = Auth::user()->id;
        $user = User::find($userID);

        return view('search/advancedsearch')->with([
            'issues' => $this->issues, 
            'products' => $this->products,
            'users' => $this->users,
            'companies' => $this->companies,
            'itemstatus' => $itemstatus,
            'user' => $user,
        ]);
    }

    public function print_search_result_pdf(Request $request) {
        $dompdf = new Dompdf();
        $rmaTicket = RmaTickets::query();
        
        $rma_ids = $request->rma_ids ? collect(
            json_decode($request->rma_ids)
        )->map(fn ($item) => $item->id) : [];

        //dd($rma_ids);   

        if ($rma_ids && count($rma_ids) === 0) {
            return;
        }
        
        
        $rmaTicket->whereIn('id', $rma_ids);

        $getRmaTickets = $rmaTicket->orderBy('created_at', 'DESC');
        $rmaTickets = $rmaTicket->get();
        $_rmaTickets = [];

        foreach ($rmaTickets as $rmaTicket) {

            $rmaTicket->faulty_total = count(RmaTickets::find($rmaTicket->id)->items);

            $_rmaTickets[] = $rmaTicket;
        }

        $html = view('search.pdf-search', ['rmaTickets' => $_rmaTickets]);

        $dompdf->loadHtml($html);

        $dompdf->setPaper('A4', 'landscape');

        $dompdf->render();

        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

    }

    public function print_search_result_csv(Request $request) {
         $array = str_replace('"id":', '', $request->rma_ids);
         $array = str_replace('{', '', $array);
         $array = str_replace('}', '', $array);
         $array = str_replace('[', '', $array);
         $array = str_replace(']', '', $array);
         if($array != '') {
            $array = explode(',', $array);
         }
         
        //  var_dump($array);
        //  return;
        $rmaTicket = RmaTickets::query();
        if($array != '' && count($array) > 0) {
         
            $rmaTicket->whereIn('id', $array);
        }
        elseif(Auth::user()->hasRole(['admin']) != true && Auth::user()->hasRole(['staff']) != true ) {
            $company = Auth::user()->company_id;
            $rmaTicket->where('company_id', $company);
        }
        $getRmaTickets = $rmaTicket->orderBy('created_at', 'DESC');
        $rmaTickets = $getRmaTickets->get();
        //  var_dump($rmaTickets);
        //  return;
        
        $rows = $rmaTickets;
        $row_name = 'rma';
        $date = [date("m"), date("d"), date("Y")];

        $csv = $rows->reduce(
            function ($data, $row) {
                $faulty_items = $row->items->count();
                $data[] = [
                    "R$row->id",
                    $row->requested_date,
                    $row->requester_email,
                    $row->requester_name,
                    $row->requester_company,
                    $row->status,
                    $row->country,
                    $faulty_items
                ];
                return $data;
            },
            [
                [
                    trans('RMA #'),
                    trans('Date Requested'),
                    trans('Requester Email'),
                    trans('Requester Name'),
                    trans('Company Name'),
                    trans('Status'),
                    trans('Country'),
                    trans('Total Faulty Items'),
                ]
            ]
        );
        return new StreamedResponse( 
            function () use ($csv) {

                $handle = fopen('php://output', 'w');
                foreach ($csv as $row) {
                    fputcsv($handle, $row);
                }

                fclose($handle);
            },
            200,
            [
                'Content-type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$row_name}_list_{$date[0]}_{$date[1]}_{$date[2]}.csv"
            ]
        );


    }

    public function search(Request $request) {

        return view('repairs/search')->with([
            'repairs' => '', 
          ]); 
    }

    public function searchRMANew(Request $request) {
        if( request()->has('items') )  {
    		$paginate = request('items');
    	} else {
    		$paginate = 15;
    	}

        $po_number = $request->po_number;

        //$repairs = $po_number;

        //Build query
        // $repairs = RmaTickets::where('po_number', $po_number)
        // ->orderBy('id', 'DESC')
        // ->paginate($paginate)->appends(['items'=> request('items')]);

    //   $repairs = RmaTickets::where('requester_email', 'LIKE', '%' . $po_number . '%')     
    //     ->orderBy('id', 'DESC')
    //     ->paginate($paginate)->appends(['items'=> request('items')]);

        $repairs = RmaTickets::select('*')
            ->where(function( $query ) use ( $po_number){
                    //$query->where('user_id', $userId);
                    $query->where('requester_email', $po_number);
                    
            })
            ->orderBy('id', 'DESC')
            ->paginate($paginate)->appends(['items'=> request('items')]);
    

        //dd($repairs);

        return view('repairs/search')->with([
            'repairs' =>  $repairs, 
        ]); 
    }

    public function advanced_search_rma(Request $request) {
        $rma_ids = "";
        $id = Auth::user()->id;
        if( request()->has('items') )  {
    		$paginate = request('items');
    	} else {
    		$paginate = 15;
    	}
        $searchGroup = Search::where('user_id', $id)->get();

        if(request('filter_id') && request('filter_id') != '') {
            $defaultSearch = request('filter_id');
            $editDefaultSearch = Search::where('id', request('filter_id'))->first();
          }
          elseif(request('filter_id') == 0) {
            $defaultSearch = false;
            $editDefaultSearch = false;
          
          }
          else {
            $defaultSearch = SearchDefault::where('user_id', $id)->value('search_id');
            $editDefaultSearch = Search::where('id', $defaultSearch)->first();
    
          }

        $repairs = RmaTickets::where('requester_email', 'julyninocabigas@gmail.com')
        ->orderBy('id', 'DESC')
        ->paginate(5);

        $rmaTicket = RmaTickets::query();
        $name  = $request->input('name');
        $rma_number  = $request->input('rma_number');
        $requester_id  = $request->input('requester_id');
        $from  = $request->input('from');
        $to  = $request->input('to');
        $status = $request->input('status');
        $po_number = $request->input('po_number');
        $company_id = $request->input('company_name');
        $country = $request->input('country');
        $faults = $request->input('faults');
        $itemStatus = $request->input('itemStatus');
        $serial_number = $request->input('serial_number');
        $other = $request->input('other'); 
        $other_value = $request->input('other_value');
        $userId = Auth::user()->id;
        $model = $request->input('model');
        $filterType = $request->input('filter_type');
        

        if ($rma_number) {
            $rmaTicket->where('id', $rma_number);
        }

        if ($requester_id) {
            $rmaTicket->where('user_id', $requester_id);
        }

        if ($status) {
            $rmaTicket->where('status', $status);
        }

        if ($po_number) {
            $rmaTicket->where('po_number', $po_number);
        }

        if ($company_id) {
            $rmaTicket->where('company_id', $company_id);
        }

        if ($country) {
            $rmaTicket->where('country', $country);
        }
  
        if ($from) {
            $rmaTicket->whereDate('requested_date', '>=', $from);
        }

        if ($to) {
            $rmaTicket->whereDate('requested_date', '<=', $to);
        }


        if ($model) {
            $rmaItems = RmaItems::where('model', $model)
                            ->where('rma_id', '!=', NULL)
                            ->get();

            $rma_ids = collect($rmaItems)->map(fn ($rmaItem) => $rmaItem->rma_id);
            $rmaTicket->whereIn('id', $rma_ids);
        }

        if ($serial_number) {
            $rmaItems = RmaItems::where('serial_number', $serial_number)
                            ->where('rma_id', '!=', NULL)
                            ->get();

            $rma_ids = collect($rmaItems)->map(fn ($rmaItem) => $rmaItem->rma_id);
            $rmaTicket->whereIn('id', $rma_ids);
        }


        if ($request->input('order_by') && $request->input('sort_direction')) {
            $getRmaTickets = $rmaTicket->orderBy(request('order_by'), request('sort_direction'));
        } else {
            $getRmaTickets = $rmaTicket->orderBy('created_at', 'DESC');
        }


        if(!Auth::user()->isAdmin()) {
            //$userCompanies = UserCompanies::where('user_id', Auth::user()->id)->get();
            $userCompanies = false;
        } else {
            $userCompanies = false;
        }


        $getRmaTickets = $rmaTicket->orderBy('created_at', 'DESC');

        $repairs = $getRmaTickets->paginate($paginate)->appends('product',  request('product') );
        $rma_ids = $getRmaTickets->get('id');

        return view('search/search-results')->with(
            [
            'repairs' => $repairs, 
            'totalitems' => $paginate,
            'searchGroup' => $searchGroup,
            'defaultSearch' => $defaultSearch,
            'users' => $this->users,
            'companies' => $this->companies, 
            'userCompanies' => $userCompanies,
            'editSearch' => $editDefaultSearch,
            'rma_ids' => $rma_ids, 
            'products' => $this->products, 
             ]);   
    }

    public function searchRepair(Request $request) {
       
        if($request->ajax() && Auth::user()->isAdmin() ) {
            
            $id      = Auth::user()->id;
            $search  = $request->input('search');
            $search_type = $request->input('search_type');
            $page    = 
            $output  = "";
  
            // Set pagination 
            if( request()->has('items') )  {
                $paginate = request('items');
            } else {
                $paginate = 15;
            }
            if( request()->has('page') )  {
                $page = request('page');
            } else {
                $page = 1;
            }
  
            $output = "";
  
            if( empty( $request->input('search') ) ) {
  
                $repairs = RmaTickets::orderBy('created_at', 'DESC')
                            ->paginate($paginate)->appends(['items'=> request('items'),'page' => $page]);


            } else if ( !empty($request->input('search') ) ) {
  
                    // $repairs = RmaTickets::where('id',  'LIKE', '%' . $search . '%' )
                    //         ->orderBy('created_at', 'DESC')
                    //         ->paginate($paginate)->appends(['items'=> request('items'),'page' => $page]);
                            
                        $rmaTicket = RmaTickets::query();
                       
                        //Check RMA number    
                        if($search_type == 'rma_id') {
                            $rmaNumbers = RmaTickets::where('id',  'LIKE', '%' . $search . '%' )
                                            ->get();
                            $rma_ids = collect($rmaNumbers)->map(fn ($rmaItem) => $rmaItem->id);              
                            $rmaTicket->whereIn('id', $rma_ids);
                        
                        }

                        //Check SN from fault items
                        if($search_type == 'serial_number') {
                            $rmaItems = RmaItems::where('serial_number', 'LIKE', '%' . $search . '%' )
                                            ->where('rma_id', '!=', NULL)
                                            ->get();
                
                            $item_rma = collect($rmaItems)->map(fn ($rmaItem) => $rmaItem->rma_id);
                            $rmaTicket->whereIn('id', $item_rma);
                        }
                        
                    
                        $getRmaTickets = $rmaTicket->orderBy('created_at', 'DESC');
                        $repairs = $getRmaTickets->paginate($paginate)->appends(['items'=> request('items'),'page' => $page]);

                
                    
            } else {
                $repairs = '';
            }              
  
            if( $repairs )
            {
                  foreach( $repairs as $repair ) {
  
                          //$repair->is_warranty = ( $repair->under_warranty ) ? '<span class="fa fa-check"></span>': '';
                          //$repair->status_color = $this->statusColor($repair->status);
                           $repair->totalItems = $repair->items->count();
                          //$repair->link = url('admin/repairs') . '/' . $repair->id; 
  
                  }
            }
            else {
                $repairs = 'N/A';
            }              
  
            $response = [
                  'repairs' => $repairs
                ];
  
              return $response;
  
        }
    }

    public function show_search_results()
    {	
        if( request()->has('items') )  {
            $paginate = request('items');
          } else {
            $paginate = 15;
          }
      
      
          $id = Auth::user()->id;
      
          if( Auth::user()->isAdmin() ) {
      
              $repairs = RmaTickets::orderBy('created_at', 'DESC')
                          ->paginate($paginate)->appends('items',  request('items'));
      
            //  $repairs = DB::table('rma_tickets')
            //               ->orderBy('created_at', 'DESC')
            //               ->paginate($paginate)->appends('items',  request('items'));
          }
          else if ( Auth::user()->hasRole(['customer']) ) {
            $created_by = Auth::user()->id;
      
            $repairs = RmaTickets::where('user_id', $id)
                      ->orWhere('company_id', Auth::user()->company_id)
                      ->orderBy('created_at', 'DESC')
                      ->paginate($paginate)->appends('items',  request('items'));
          }   
          if(empty($repairs))  {
                  $repairs = null;
          } 
          return view('repairs/index')->with(['issues' => $this->issues, 'products' => $this->products, 'repairs' => $repairs, 'totalitems' => $paginate, 'issues' => $this->issues ]); 
    }


    public function repairs()
    {	
    	if( request()->has('items') )  {
    		$paginate = request('items');
    	} else {
    		$paginate = 15;
    	}

    	if( request()->has('status') )  {
	   		$repairs = Repairs::where('status', request('status') )
		   				->orderBy('created_at', 'DESC')
	   					->paginate($paginate)
	   					->appends('status',  request('status'));
    	}

    	if( request()->has('product') ) {
			$repairs = Repairs::where('product', request('product') )
						->orderBy('created_at', 'DESC')
						->paginate($paginate)
						->appends('product',  request('product'));
    	}
    	if( request()->has('company') ) {
			$repairs = Repairs::where('company','LIKE', '%' . request('company') . '%' )
						->orderBy('created_at', 'DESC')
						->paginate($paginate)
						->appends('company',  request('company'));
    	}

    	if( request()->has('product') && request()->has('status') ) {

    		$product = request('product');
    		$status  = request('status');

    		$repairs = Repairs::where( function($query) use ($product, $status) {
    							$query->where('status', $status)
    								  ->where('product', $product);	
    					})
    					->orderBy('created_at', 'DESC')->paginate($paginate)
    					->appends('product',  request('product'));
    	}

		if( request()->has('product') && request()->has('status') && request()->has('company') ) {

    		$product = request('product');
    		$status  = request('status');

    		$repairs = Repairs::
    					where('company','LIKE', '%' . request('company') . '%' )
    					->where( function($query) use ($product, $status) {
    							$query->where('status', $status)
    								  ->where('product', $product);	
    					})
    					->orderBy('created_at', 'DESC')->paginate($paginate)
    					->appends('product',  request('product'));
    	}


    	if( request()->has('product') === '' || request()->has('status') === '' || request()->has('company') === '' )  {
    		return url('repairs');
  
    	}
   
        
      return view('search/index')->with(['issues' => $this->issues, 'products' => $this->products, 'repairs' => $repairs, 'items' => $paginate ]);
 	}


    public function searchAnything(Request $request) {

        if( $request->ajax() ) {

            $status  = $request->input('search');
            $output  = "";
            $search  = $request->input('search');
            $user    = Auth::user()->id;
            $company = Auth::user()->company;

            
            if( $search ) {
                // Search, Loop and get id, name, description and link
                $searchList = array();
                
                $db_file = DB::table('files')
                            ->orWhere('filename', 'LIKE', '%' . $search . '%')
                            ->orderBy('created_at', 'DESC')
                            ->paginate(5);

                    if( $db_file ) {
                        foreach($db_file as $row) {
                            if( $row->type == 1 ) {
                                $link = URL::to('/firmwares/');
                                $category = 'Firmware';
                            } else if( $row->type == 2) {
                                $link = URL::to('/technical-documentation/');
                                $category = 'Technical Documentation';
                            } else {
                                $link = URL::to('/certificates');
                                $category = 'Certificates';
                            }
                    
                            $temp_array = [ 'id' => $row->id, 'title' => $category ,'description' => $row->filename, 'category' => $category, 'link' => $link  ];
                            array_push($searchList, $temp_array);
                        }        
                    };             


                if( Auth::user()->isAdmin() ) {

                    $db_repair = DB::table('repairs')
                            ->where('id',  'LIKE', '%' .$search  . '%' )
                            ->orWhere('company', 'LIKE', '%' . $search  . '%')
                            ->orWhere('issue', 'LIKE', '%' . $search  . '%')
                            ->orWhere('product', 'LIKE', '%' . $search  . '%')
                            ->orWhere('product_serial_no', 'LIKE', '%' . $search  . '%')
                            ->orWhere('problem_description', 'LIKE', '%' . $search  . '%')
                            ->orderBy('created_at', 'DESC')
                            ->paginate(5);
                } else {

                    $db_repair = DB::table('repairs')
                            ->where(function($query) use ($user, $company ) {
                                $query->where('user_id', $user)
                                      ->orWhere('company', $company );
                            })
                            ->where(function($query) use ($search ) {
                                $query->where('id',  'LIKE', '%' .$search . '%' )
                                        ->orWhere('company', 'LIKE', '%' . $search . '%')
                                        ->orWhere('issue', 'LIKE', '%' . $search . '%')
                                        ->orWhere('product', 'LIKE', '%' . $search . '%')
                                        ->orWhere('product_serial_no', 'LIKE', '%' . $search . '%')
                                        ->orWhere('problem_description', 'LIKE', '%' . $search . '%');
                            })
                            ->orderBy('created_at', 'DESC')
                            ->paginate(5);  

                        if( $db_repair ) {  
                            foreach($db_repair as $row) {

                                $link = URL::to('/repairs/' . $row->id . '');
                                $temp_array = [ 'id' => $row->id, 'title' => 'Ticket ID ' . $row->id ,'description' => $row->product . ' has '. $row->issue . ' issue', 'category' => 'repairs', 'link' => $link  ];

                                array_push($searchList, $temp_array);
                            };    
                        }     

                }

        
                if( Auth::user()->isAdmin() ) {


                    // Search from users table
                    $db_user = DB::table('users')
                             ->where('firstname',  'LIKE', '%' . $search  . '%' )  
                             ->orWhere('lastname',  'LIKE', '%' . $search  . '%' )    
                             ->orWhere('email',  'LIKE', '%' . $search  . '%' )  
                             ->orWhere('company',  'LIKE', '%' . $search  . '%' )
                             ->paginate(5); 

                } else {


                    $db_user = DB::table('users')
                            ->where(function($query) use ($user, $company ) {
                                $query->where('company', $company );
                            })
                            ->where(function($query) use ($search ) {
                                $query->where('firstname',  'LIKE', '%' . $search  . '%' )  
                                     ->orWhere('lastname',  'LIKE', '%' . $search  . '%' )    
                                     ->orWhere('email',  'LIKE', '%' . $search  . '%' )  
                                     ->orWhere('company',  'LIKE', '%' . $search  . '%' );
                            })
                            ->orderBy('created_at', 'DESC')
                            ->paginate(5);                    
                
                    if( $db_user ) {
                        foreach($db_user as $row) {
                            $link = URL::to('/profile/' . $row->id . '');
                            $temp_array = [ 'id' => $row->id, 'title' => $row->firstname .' ' . $row->lastname ,'description' => 'Country :' . $row->country . ' Company : '. $row->company . '', 'category' => 'users', 'link' => $link  ];

                            array_push($searchList, $temp_array);
                        }; 
                    }       

                }

                    
                if( Auth::user()->isAdmin() ) {
                     // Search from task table
                    $db_task = DB::table('software_tickets')
                             ->where('type',  'LIKE', '%' .$search  . '%' )  
                             ->orWhere('summary',  'LIKE', '%' .$search  . '%' )    
                             ->orWhere('description',  'LIKE', '%' .$search  . '%' )  
                             ->orWhere('status',  'LIKE', '%' .$search  . '%' )
                             ->paginate(5); 
                } else {
                    $db_task = DB::table('software_tickets')
                            ->where(function($query) use ($user, $company ) {
                                $query->where('company', $company );
                            })
                            ->where(function($query) use ($search ) {
                                $query->where('type',  'LIKE', '%' .$search  . '%' )  
                                     ->orWhere('summary',  'LIKE', '%' .$search  . '%' )    
                                     ->orWhere('description',  'LIKE', '%' .$search  . '%' )  
                                     ->orWhere('status',  'LIKE', '%' .$search  . '%' );
                            })
                            ->orderBy('created_at', 'DESC')
                            ->paginate(5);   

                    if( $db_task ) {
                        foreach($db_task as $row) {

                            $link = URL::to('/admin/softwares/' . $row->id . '');

                            $temp_array = [ 'id' => $row->id, 'title' => 'Task ID ' . $row->id,'description' => $row->summary, 'category' => 'tasks', 'link' => $link  ];

                            array_push($searchList, $temp_array);

                        };  
                    }       


                }
           
                return Response($searchList);             

            }
            else {
                $searchList = '';
            }              
                
             return Response($searchList);         
        }
    }

    public function destroy($id, Request $request) 
    {
    
        if(Auth::user()->isAdmin()) {
           
             
             Search::find($id)->delete();
             SearchDefault::where('search_id', $id)->delete();

            $request->session()->flash('alert-danger', 'Filter group has been successfully deleted!');

            return redirect('repairs');

        }
        else {

             return response()->view('errors.403');
        }


    } 
}
