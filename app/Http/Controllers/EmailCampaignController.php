<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Redirect;
use App\Models\Option;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmailCampaignController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $_user = DB::table('users');
        $_company = DB::table('companies');

        if ($search = $request->search) {
            $_user->where(function ($query) use($search, $_company) {
                $query->where('firstname', 'like', "%$search%")
                    ->orWhere('lastname', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");

                $companyQuery = $_company->select('id')->where('name', 'like', "%$search%");

                if ($companyQuery->count() > 0) {
                    $query->orWhereIn('company_id', $companyQuery);
                }
            });
        }

        $subscribers = $_user
            ->where('subscribe', 1)
            ->orderBy('created_at', 'DESC')
            ->paginate(25);

        $pagination = json_decode(
            json_encode($subscribers)
        )->links;
            
        return view('campaign/index')->with([
            'subscribers' => $subscribers,
            'pagination' => $pagination
        ]);  
    }


    public function unsubscribing($id) {

        $user_id = strip_tags($id);

       // // $subscriber = DB::table('users')
       //                  ->where('id', $user_id)
       //                  ->select('id','firstname','lastname', 'company', 'email')
       //                  ->get();
        $subscriber = User::find($id);

        return view('campaign/show-subscriber')->with('subscriber', $subscriber);  
    }

     /**
     * Display a listing of all unsubscribes.
     *
     * @return \Illuminate\Http\Response
     */
    public function unsubscribes()
    {
        $subscribers = DB::table('users')
                        ->where('subscribe', NULL)
                        ->orWhere('subscribe', 0)
                        ->orderBy('created_at', 'DESC')
                        ->paginate(25);

        $pagination = json_decode(
            json_encode($subscribers)
        )->links;
            
        return view('campaign/unsubscribe')->with([
            'subscribers' => $subscribers,
            'pagination' => $pagination,
        ]);  
    }

    /**
     * Unsubscibe user from the email notifications
     * @param  Request $request 
     * @param  Int     $id     
     * @return View
     */
    public function subscribeUser(Request $request, $id)
    {
        DB::table('users')
                ->where('id', $id)  
                ->update(['subscribe' => 1]);
            
        $request->session()->flash('alert-success', 'User has been successfully subscribed!');
            
        return redirect()->route('emailcampaign.unsubscribes'); 
    }

    /**
     * Search for subscribe users
     * @param  Request $request 
     * @return [type]          
     */
    public function searchSubscriber(Request $request)
    {

       if($request->ajax()) {

            // URL Parameters
            $search    = strip_tags( $request->input('search') ); 

            $output    = "";

        
            if( $request->input('search') ) {

                // Search all users            
                $users = DB::table('users')
                        ->where('subscribe', 1 )
                        ->where( function( $query ) use ( $search ){
                                $query->where('firstname', $search )
                                      ->orWhere('lastname', $search )    
                                      ->orWhere('company', $search );          
                        })
                        ->select('id','firstname','lastname', 'company', 'email')
                        ->get();


                $software_tickets = $users;

            }  
            else {

                $software_tickets = '';
                 
            }

            $response = [
                  'contacts' => $software_tickets,
                  'search_for' => $search
            ];

            return $response;

        }  
                      
    }


    /**
     * Unsubscibe user from the email notifications
     * @param  Request $request 
     * @param  Int     $id     
     * @return View
     */
    public function update(Request $request, $id)
    {
        DB::table('users')
                ->where('id', $id)  
                ->update(['subscribe' => NULL]);
            
        $request->session()->flash('alert-success', 'User has been successfully unsubscribed!');
            
        return redirect()->route('campaign.index'); 
    }



    public function updateGeneral(Request $request)
    {
 
        $email_footer = Option::where('key','email_footer' )->value('value');

        if(isset( $email_footer ) ) {
           $option = DB::table('options')
                ->where('key','email_footer' )
                ->update(['value' => $request->input('address')  ]);
        } else {  
            $option = new Option();
            $option->key = 'email_footer';
            $option->value = $request->input('address');
            $option->save();
        }

        if($option) {

           $request->session()->flash('alert-success', 'Email Footer has been successfully added!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function updateNewTicketAdmin(Request $request)
    {
 
        $subject = Option::where('key','new_ticket_admin_subject' )->value('value');
        $body    = Option::where('key','new_ticket_admin_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_admin_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_admin_subject';
            $option->value = $request->input('subject');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_admin_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_admin_body';
            $option->value = $request->input('body');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function updateNewTicketCustomer(Request $request)
    {
 
        $subject = Option::where('key','new_ticket_customer_subject' )->value('value');
        $body    = Option::where('key','new_ticket_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }




    public function updateNewTaskAdmin(Request $request)
    {
 
        $subject = Option::where('key','new_task_admin_subject' )->value('value');
        $body    = Option::where('key','new_task_admin_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_task_admin_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_task_admin_subject';
            $option->value = $request->input('subject');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_task_admin_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_task_admin_body';
            $option->value = $request->input('body');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function updateNewTaskCustomer(Request $request)
    {
 
        $subject = Option::where('key','new_task_customer_subject' )->value('value');
        $body    = Option::where('key','new_task_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_task_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_task_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_task_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_task_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }

    public function updateNewQuotationCustomer(Request $request)
    {
 
        $subject = Option::where('key','new_ticket_customer_subject' )->value('value');
        $body    = Option::where('key','new_ticket_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_ticket_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_ticket_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function updateNewFile(Request $request)
    {
 
        $subject1  = Option::where('key','new_file_customer_subject1' )->value('value');
        $subject2 = Option::where('key','new_file_customer_subject2' )->value('value');
        $subject3 = Option::where('key','new_file_customer_subject3' )->value('value');
        $body     = Option::where('key','new_file_customer_body' )->value('value');

        if(isset( $subject1 ) ) {
           $option = DB::table('options')
                ->where('key','new_file_customer_subject1' )
                ->update(['value' => $request->input('subject1')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_file_customer_subject1';
            $option->value = $request->input('subject1');
            $option->save();
        }

        if(isset( $subject2 ) ) {
           $option = DB::table('options')
                ->where('key','new_file_customer_subject2' )
                ->update(['value' => $request->input('subject2')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_file_customer_subject2';
            $option->value = $request->input('subject2');
            $option->save();
        }

        if(isset( $subject3 ) ) {
           $option = DB::table('options')
                ->where('key','new_file_customer_subject3' )
                ->update(['value' => $request->input('subject3')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_file_customer_subject3';
            $option->value = $request->input('subject3');
            $option->save();
        }


        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_file_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_file_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }


       
           $request->session()->flash('alert-success', 'Email Notification for file updates has been successfully updated!');
           return back();
     
    }

     public function updateTaskResolve(Request $request)
    {

        $subject = Option::where('key','task_resolve_customer_subject' )->value('value');
        $body    = Option::where('key','task_resolve_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','task_resolve_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_resolve_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','task_resolve_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_resolve_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }

        $request->session()->flash('alert-success', 'Email Notification for resolved task has been successfully updated!');
        return back();

    }

    public function newRegistrationAdmin(Request $request)
    {
 
        $subject = Option::where('key','new_registration_admin_subject' )->value('value');
        $body    = Option::where('key','new_registration_admin_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_registration_admin_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_registration_admin_subject';
            $option->value = $request->input('subject');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_registration_admin_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_registration_admin_body';
            $option->value = $request->input('body');
            $option->save();
        }

        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function newRegistrationCustomer(Request $request)
    {
 
        $subject = Option::where('key','new_registration_customer_subject' )->value('value');
        $body    = Option::where('key','new_registration_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','new_registration_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_registration_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','new_registration_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'new_registration_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }

        if($option) {
           $request->session()->flash('alert-success', 'Email Notification for New Registration has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }
    }


    public function newTaskCommentCustomer(Request $request)
    {
 
        $subject = Option::where('key','task_comment_customer_subject' )->value('value');
        $body    = Option::where('key','task_comment_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','task_comment_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_comment_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','task_comment_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_comment_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }

        if($option) {
           $request->session()->flash('alert-success', 'Email Notification has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }

    }


    public function newTaskStatus(Request $request)
    {

        //Remove description
        $subject = Option::where('key','task_remove_description_subject' )->value('value');
        $body    = Option::where('key','task_remove_description_body' )->value('value');

         if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','task_remove_description_subject' )
                ->update(['value' => $request->input('subject_remove_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_remove_description_subject';
            $option->value = $request->input('subject_remove_description');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','task_remove_description_body' )
                ->update(['value' => $request->input('body_remove_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_remove_description_body';
            $option->value = $request->input('body_remove_description');
            $option->save();
        }


        //New description
        $subject2 = Option::where('key','task_new_description_subject' )->value('value');
        $body2    = Option::where('key','task_new_description_body' )->value('value');

         if(isset( $subject2 ) ) {
           $option = DB::table('options')
                ->where('key','task_new_description_subject' )
                ->update(['value' => $request->input('subject_new_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_new_description_subject';
            $option->value = $request->input('subject_new_description');
            $option->save();
        }

        if(isset( $body2 ) ) {
           $option = DB::table('options')
                ->where('key','task_new_description_body' )
                ->update(['value' => $request->input('body_new_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_new_description_body';
            $option->value = $request->input('body_new_description');
            $option->save();
        }


        //Update description
        $subject3 = Option::where('key','task_update_description_subject' )->value('value');
        $body3    = Option::where('key','task_update_description_body' )->value('value');

         if(isset( $subject3 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_description_subject' )
                ->update(['value' => $request->input('subject_update_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_description_subject';
            $option->value = $request->input('subject_update_description');
            $option->save();
        }

        if(isset( $body3) ) {
           $option = DB::table('options')
                ->where('key','task_update_description_body' )
                ->update(['value' => $request->input('body_update_description')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_description_body';
            $option->value = $request->input('body_update_description');
            $option->save();
        }


        //Update Type
        $subject4 = Option::where('key','task_update_type_subject' )->value('value');
        $body4    = Option::where('key','task_update_type_body' )->value('value');

         if(isset( $subject4 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_type_subject' )
                ->update(['value' => $request->input('subject_type')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_type_subject';
            $option->value = $request->input('subject_type');
            $option->save();
        }

        if(isset( $body4 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_type_body' )
                ->update(['value' => $request->input('body_type')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_type_body';
            $option->value = $request->input('body_type');
            $option->save();
        }


        //Update Type
        $subject5 = Option::where('key','task_update_summary_subject' )->value('value');
        $body5    = Option::where('key','task_update_summary_body' )->value('value');

         if(isset( $subject5 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_summary_subject' )
                ->update(['value' => $request->input('subject_summary')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_summary_subject';
            $option->value = $request->input('subject_summary');
            $option->save();
        }

        if(isset( $body5 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_summary_body' )
                ->update(['value' => $request->input('body_summary')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_summary_body';
            $option->value = $request->input('body_summary');
            $option->save();
        }



        //Update Product
        $subject6 = Option::where('key','task_update_product_subject' )->value('value');
        $body6    = Option::where('key','task_update_product_body' )->value('value');

         if(isset( $subject6 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_product_subject' )
                ->update(['value' => $request->input('subject_product')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_product_subject';
            $option->value = $request->input('subject_product');
            $option->save();
        }

        if(isset( $body6 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_product_body' )
                ->update(['value' => $request->input('body_product')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_product_body';
            $option->value = $request->input('body_product');
            $option->save();
        }


        //Update Status
        $subject8 = Option::where('key','task_update_status_subject' )->value('value');
        $body8    = Option::where('key','task_update_status_body' )->value('value');

         if(isset( $subject8 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_status_subject' )
                ->update(['value' => $request->input('subject_status')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_status_subject';
            $option->value = $request->input('subject_status');
            $option->save();
        }

        if(isset( $body8 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_status_body' )
                ->update(['value' => $request->input('body_status')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_status_body';
            $option->value = $request->input('body_status');
            $option->save();
        }

        //Update Assignee
        $subject7 = Option::where('key','task_update_assignee_subject' )->value('value');
        $body7    = Option::where('key','task_update_assignee_body' )->value('value');

         if(isset( $subject7 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_assignee_subject' )
                ->update(['value' => $request->input('subject_assignee')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_assignee_subject';
            $option->value = $request->input('subject_assignee');
            $option->save();
        }

        if(isset( $body7 ) ) {
           $option = DB::table('options')
                ->where('key','task_update_assignee_body' )
                ->update(['value' => $request->input('body_assignee')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_update_assignee_body';
            $option->value = $request->input('body_assignee');
            $option->save();
        }


        if($option) {
           $request->session()->flash('alert-success', 'Email Notification for new attachment has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }



    }


    public function newTaskAttachment(Request $request)
    {
        $subject = Option::where('key','task_attachment_customer_subject' )->value('value');
        $body    = Option::where('key','task_attachment_customer_body' )->value('value');

        if(isset( $subject ) ) {
           $option = DB::table('options')
                ->where('key','task_attachment_customer_subject' )
                ->update(['value' => $request->input('subject')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_attachment_customer_subject';
            $option->value = $request->input('subject');
            $option->save();
        }

        if(isset( $body) ) {
           $option = DB::table('options')
                ->where('key','task_attachment_customer_body' )
                ->update(['value' => $request->input('body')  ]);
        } else {  
            $option = new Option();
            $option->key = 'task_attachment_customer_body';
            $option->value = $request->input('body');
            $option->save();
        }

        if($option) {
           $request->session()->flash('alert-success', 'Email Notification for new attachment has been successfully updated!');
           return back();
        } else  {
           return redirect('admin/email-setting');
        }

    }




}
