<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use DB;
use App\Models\Option;

class RepairItemChanges extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    protected $created_by;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair, $fields, $requester_name)
    {
        $this->repair = $repair;
        $this->fields = $fields;
        $this->created_by = $requester_name;
        $this->name  =  $requester_name;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

  
        $subject = Option::where('key','new_ticket_admin_subject' )->value('value'); 

        return $this->from('support@pacom.com')
                    ->subject('Ticket R'. $this->repair . ' has updated a faulty item')
                    ->view('emails/repair/itemchanges')
                    ->with([

                        'created_by' => $this->created_by, 
                        'firstname' => $this->name,
                        'fields' => $this->fields,
                        'rma_no' => $this->repair,

                    ]);
    }
}
