<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemStatus;
use Auth;
use App\Models\UserLogs;

class ItemStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$itemstatus = ItemStatus::orderBy('created_at','DESC')->paginate(10);

        $pagination = json_decode(
            json_encode($itemstatus)
        )->links;

        return view('itemstatus/index')->with(['itemstatus' => $itemstatus, 'pagination' => $pagination]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('itemstatus/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       // Validating Input Fields
       $this->validate($request, [
            'name' => 'required|unique:rma_item_status',
        ]);


        $created_by = Auth::user()->id;

        $itemstatus                    = new ItemStatus;
        $itemstatus->name              = strip_tags( $request->input('name') );
        $itemstatus->description       = strip_tags(  $request->input('description') );
        $itemstatus->save();

        $request->session()->flash('alert-success', 'Item status has been successfully added!');

        return redirect('admin/itemstatus')->with('root cause', 'added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $itemstatus = ItemStatus::findorfail($id);

        if( $itemstatus ) {
            return view('itemstatus/show')->with('itemstatus', $itemstatus );
        } else {
            return view('errors.404');
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
        $itemstatus = ItemStatus::findorfail($id);

        if( $itemstatus ) {
            return view('itemstatus/edit')->with('itemstatus', $itemstatus );
        } else {
            return view('errors.404');
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
        // Validating Input Fields
        $this->validate($request, [
            'name' => 'required',

        ]);

        ItemStatus::where('id', $id)->update(['name' => strip_tags( $request->input('name')), 'description' => strip_tags( $request->input('description') ) ]);

        $request->session()->flash('alert-success', 'Item status has been successfully added!');

        return redirect('admin/itemstatus')->with('itemstatus', 'added');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id,Request $request)
    {
        if(Auth::user()->isAdmin()) {

            $itemstatus = ItemStatus::find($id)->value('name');
           
            ItemStatus::find($id)->delete();

            $logs = new UserLogs();   
            $logs->type = '<span class="bg-danger">Item Status Deleted</span>'; 
            $logs->description = 'Deleted <strong>' . $itemstatus . '</strong> from the root cause, ';
            $logs->old_value = $itemstatus;
            $logs->new_value = '';
            $logs->action = 'deleted';
            $logs->user_id = Auth::user()->id; 
            $logs->created_by = Auth::user()->id; 
            $logs->save();


           $request->session()->flash('alert-danger', 'Item status has been successfully deleted!');

           return redirect('admin/itemstatus');

       }
       else {

            return response()->view('errors.403');
       }

    }
}
