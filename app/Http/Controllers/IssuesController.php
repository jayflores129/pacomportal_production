<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Issues;
use DB;
use Redirect;
use Auth;
use App\Models\UserLogs;

class IssuesController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    //

    public function index()
    {

    	$issues = DB::table('issues')->orderBy('created_at', 'desc')->paginate(10);

        $pagination = json_decode(
            json_encode($issues)
        )->links;

        return view('issues/index')->with([
            'issues' => $issues, 
            'pagination' => $pagination
        ]);
    }

    public function create()
    {

      if(Auth::user()->isAdmin()) {
            return view('/issues/create');
      }
      else {
         return response()->view('errors.403');
      }
        
    }
    public function show($id) {
        $issue = Issues::findorfail($id);
        if( $issue ) {
            return view('/issues/edit')->with('issue', $issue);
        } else {
            return view('errors.404');
        }
    }

    public function store(Request $request) {


      if(Auth::user()->isAdmin()) {

            // Validating Input Fields
            $this->validate($request, [
                'issue' => 'required',

            ]);

            $created_by = Auth::user()->id;

            //saving data
            $issue = new Issues;
            $issue->name = strip_tags(  $request->input('issue') );
            $issue->description = strip_tags(  $request->input('description') );
            $issue->user_id = $created_by;
            $issue->save();

            $request->session()->flash('alert-success', 'Issue has been successfully added!');

            return redirect('admin/issues');
      }
      else {
             return response()->view('errors.403');
      }

    }


    public function destroy( $id, Request $request ) {

        if( Auth::user()->isAdmin() ) {

            $issue = Issues::find($id)->value('name');
            
            Issues::find($id)->delete();

             $logs = new UserLogs();   
             $logs->type = '<span class="bg-danger">Issue Deleted</span>'; 
             $logs->description = 'Deleted <strong>' . $issue . '</strong> from product issues ';
             $logs->old_value = $issue;
             $logs->new_value = '';
             $logs->action = 'deleted';
             $logs->user_id = Auth::user()->id; 
             $logs->created_by = Auth::user()->id; 
             $logs->save();

            $request->session()->flash('alert-danger', 'Issue has been successfully deleted!');

            return redirect('admin/issues');

        }
        else {
             return response()->view('errors.403');
        }
    }

    /**
     * [edit description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function edit($id) {
        if( ! Auth::user()->isAdmin() ) {
            
            return redirect('/');

        }
        $issue = Issues::findorfail($id);
        if( $issue ) {
            return view('/issues/edit')->with('issue', $issue);
        } else {
            return view('errors.404');
        }
         
    }

    /**
     * [update description]
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
                'name' => 'required'
        ]); 

        Issues::where('id', $id)->update(['description' => $request->input('description')]);


        $request->session()->flash('alert-success', 'Issue has been successfully updated!');

        return redirect('/admin/issues');


    } 

}
