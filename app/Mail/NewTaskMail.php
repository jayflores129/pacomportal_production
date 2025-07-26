<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\Option;

class NewTaskMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $task;
    protected $product;
    protected $assign;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($task)
    {
           $this->task = $task;

           $this->product = DB::table('products')->where('id', $task->product_id)->value('name');
           $firstname     = DB::table('users')->where( 'id', $this->task->assigned_to )->value('firstname');
           $lastname      = DB::table('users')->where( 'id', $this->task->assigned_to )->value('lastname');

           $this->assign  = $firstname . ' ' . $lastname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        if($this->task->myRole == 'admin') {
            $subject = Option::where('key','new_task_admin_subject' )->value('value'); 
            $view    = 'emails/task/new/admin';
        } 
        elseif($this->task->myRole == 'customer')  {
            $subject = Option::where('key','new_task_customer_subject' )->value('value'); 
            $view    = 'emails/task/new/customer';
        }


        return $this
                    ->from('support@pacom.com')
                    ->subject( $subject . ' (Task #'. $this->task->id .')')
                    ->view($view)
                    ->with([
                        'type'     => $this->task->type,
                        'taskID'   => $this->task->id,
                        'assignee' => $this->assign,
                        'summary'  => $this->task->summary,
                        'product'  => $this->product,
                        'link'     => url('admin/softwares') . '/'. $this->task->id

                    ]);
    }
}
