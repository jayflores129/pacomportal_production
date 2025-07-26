<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RootCause;
use Auth;
use App\Models\UserLogs;

class RootCauseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    	$rootcause = RootCause::orderBy('created_at','DESC')->paginate(20);

        $pagination = json_decode(
            json_encode($rootcause)
        )->links;

        return view('rootcauses/index')->with(['rootcauses' => $rootcause, 'pagination' => $pagination]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('rootcauses/create');
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
            'name' => 'required|unique:rma_root_cause',
        ]);


        $created_by = Auth::user()->id;

        $rootcause                    = new RootCause;
        $rootcause->name              = strip_tags( $request->input('name') );
        $rootcause->description       = strip_tags(  $request->input('description') );
        $rootcause->save();

        $request->session()->flash('alert-success', 'Root cause has been successfully added!');

        return redirect('admin/rootcause')->with('root cause', 'added');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rootcause = RootCause::findorfail($id);

        if( $rootcause ) {
            return view('rootcauses/show')->with('rootcause', $rootcause );
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
        $rootcause = RootCause::findorfail($id);

        if( $rootcause ) {
            return view('rootcauses/edit')->with('rootcause', $rootcause );
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

        RootCause::where('id', $id)->update(['name' => strip_tags( $request->input('name')), 'description' => strip_tags( $request->input('description') ) ]);

        $request->session()->flash('alert-success', 'Root cause has been successfully added!');

        return redirect('admin/rootcause')->with('rootcauses', 'added');
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

            $rootcause = RootCause::find($id)->value('name');
           
            RootCause::find($id)->delete();

            $logs = new UserLogs();   
            $logs->type = '<span class="bg-danger">Root Cause Deleted</span>'; 
            $logs->description = 'Deleted <strong>' . $rootcause . '</strong> from the root cause, ';
            $logs->old_value = $rootcause;
            $logs->new_value = '';
            $logs->action = 'deleted';
            $logs->user_id = Auth::user()->id; 
            $logs->created_by = Auth::user()->id; 
            $logs->save();


           $request->session()->flash('alert-danger', 'Root cause has been successfully deleted!');

           return redirect('admin/rootcause');

       }
       else {

            return response()->view('errors.403');
       }

    }
}
