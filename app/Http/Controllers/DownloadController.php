<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLogs;
use Auth;
use Carbon;

class DownloadController extends Controller
{
    //
    


	/**
	 * Log download activity 
	 * @param  Request $request  Filename, downloaded is true
	 * @return string
	 */
    public function file(Request $request)
    {

    	if( $request->ajax() ) {

    			 $user = Auth::user();	

    			 if( $request->input('downloaded') && $request->input('filename') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-secondary">File download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. strip_tags( $request->input('filename') ) . '</strong> from Firmware / Software';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else {
    			 	$output = 'failed';

		         	return Response($output);
    			 }
		         
    	}
    }

	/**
	 * Log download activity [Document]
	 * @param  Request $request  Document name, downloaded is true
	 * @return string
	 */
    public function document(Request $request)
    {
		if( $request->ajax() ) {

    			 $user = Auth::user();	

    			 if( $request->input('downloaded') && $request->input('docname') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-danger">Document download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. strip_tags(  $request->input('docname') ) . '</strong> from Firmware / Software';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else {
    			 	$output = 'failed';

		         	return Response($output);
    			 }
		         
    	}
    }

	/**
	 * Log download activity [Technical Document]
	 * @param  Request $request  Technical Documentation name, downloaded is true
	 * @return string
	 */
    public function technical(Request $request)
    {
		if( $request->ajax() ) {

    			 $user = Auth::user();	

    			 if( $request->input('file') == true && $request->input('technical_doc_name') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-color-3">Technical Documentation download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. strip_tags(  $request->input('technical_doc_name') ) . '</strong> from Technical Documentation';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else if( $request->input('document') == true && $request->input('technical_doc_name') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-success">Document download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. strip_tags(  $request->input('technical_doc_name') ) . '</strong> from Technical Documentation';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else {
    			 	$output = 'failed';

		         	return Response($output);
    			 }
		         
    	}
    }

	/**
	 * Log download activity [Certificate]
	 * @param  Request $request  Certificate name, file is true
	 * @return string
	 */
    public function certificate(Request $request)
    {
		if( $request->ajax() ) {

    			 $user = Auth::user();	

    			 if( $request->input('file') == true && $request->input('certificate') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-color-6">Certificate download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. strip_tags( $request->input('certificate') ) . '</strong> from Certificates';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else {
    			 	$output = 'failed';

		         	return Response($output);
    			 }
		         
    	}
    }

	/**
	 * Log download Task
	 * @param  Request $request  Task name, file is true
	 * @return string
	 */
    public function task(Request $request)
    {
		if( $request->ajax() ) {

    			 $user = Auth::user();	

    			 if( $request->input('file') == true && $request->input('certificate') ) {

					 $activty = new UserLogs();
			         $activty->type = '<span class="box-bg bg-color-6">File download</span>'; 
			         $activty->description = '<strong>'. $user->firstname . ' ' . $user->lastname .  '</strong> has downloaded  <strong>'. $request->input('filename') . '</strong> from the task';  
			         $activty->old_value = '';
			         $activty->new_value = '';
			         $activty->action = 'user_activity';
			         $activty->user_id = $user->id; 
			         $activty->created_by = $user->id; 
			         $activty->save();


			         $output = 'success';

		         	 return Response($output);
    			 }
    			 else {
    			 	$output = 'failed';

		         	return Response($output);
    			 }
		         
    	}
    }

}
