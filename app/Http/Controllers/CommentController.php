<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use Auth;
use App\Models\RmaTickets;
use App\Models\RmaItems;
use App\Models\RmaItemFaults;
use App\Models\RmaStatus;
use App\Models\RmaComments;
use App\Models\RmaLogs;
use App\Mail\RepairComment;
use App\Traits\SendEmailRepairUpdates;
use Mail;

class CommentController extends Controller
{
    use SendEmailRepairUpdates;

    //create comment RMA
    public function create(Request $request) {

        $this->validate($request, [
            'comment' => 'required',
            'rma_id' => 'required'
        ]);

        $comment = new RmaComments;

        $comment->comment = $request->comment;
        $comment->user_id = Auth::user()->id;
        $comment->rma_id = $request->rma_id;
        $comment->save(); 

        $this->createLog(array(
            "field"   => "Comment",
            "new_val" => $request->comment,
            "action"  => "added",
            "rma_id"  => $request->rma_id,
        ));

        $requester_email = RmaTickets::where('id', $request->rma_id)->value('requester_email');
        $comment->requester_name = RmaTickets::where('id', $request->rma_id)->value('requester_name');

        $this->sendNotifiedRequesterEmail($request->rma_id, function() use ($requester_email, $comment) {
            Mail::to($requester_email)->send(new RepairComment($comment));
          });

        $request->session()->flash('alert-success', 'Comment has been successfully added!');

        return redirect('repairs/' . $request->rma_id);
    }

    private function createLog($actLog){
        $log          = new RmaLogs;
        $created_by   = Auth::user()->firstname . ' ' . Auth::user()->lastname;
        $user_id      = Auth::user()->id;
        $field        = isset($actLog["field"]) ? $actLog["field"] : ""; 
        $old_val      = isset($actLog["old_val"]) ? $actLog["old_val"] : ""; 
        $new_val      = isset($actLog["new_val"]) ? $actLog["new_val"] : ""; 
        $action       = isset($actLog["action"]) ? $actLog["action"] : ""; 
        $rma_item_id  = isset($actLog["rma_item_id"]) ? $actLog["rma_item_id"] : ""; 
        $rma_id       = isset($actLog["rma_id"]) ? $actLog["rma_id"] : "";
        $cus_desc     = isset($actLog["cus_desc"]) ? isset($actLog["cus_desc"]) : "";
        
        
        $color        = "";
        if(isset($actLog["color"])){
            $color = $actLog["color"];
        } else if($action === "added"){
            $color = "3";
        } else if($action === "updated"){
            $color = "1";
        } else if($action === "deleted"){
            $color = "4";
        } else {
            $color = "5";
        }

        
  
        if($rma_id == '' &&  $rma_item_id != '') {
          $log->rma_id = $rma_item_id;
        } else {
          $log->rma_id = $rma_id;
        }


        // $trimText = "";
        // if (strlen($new_val) > 15) // if you want...
        // {
        //     $maxLength = 14;
        //     $text = substr($new_val, 0, $maxLength);
        //     $trimText = $text  . ' ... ';
        // } else {
        //     $trimText = $new_val;
        // }
  
        $log->type = '<span class="box-bg bg-color-'. $color .'">' .  $field . ' '.  $action . '</span>'; 
        
        $log->description =  $cus_desc ? $cus_desc : $field . '<strong></strong> was <strong>'.  $action . ' </strong> by <strong>#'. $created_by . '</strong>' ;
       
  
        $log->old_value   = $old_val;
        $log->new_value   = $new_val;
        $log->action      = $action;
        $log->user_id     = $user_id; 
        $log->created_by  = $created_by; 
        $log->save();
    }
}
