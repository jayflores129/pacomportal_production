<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use DB;
use App\Models\Option;

class RepairChanges extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    protected $created_by;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair, $fields)
    {
        $this->repair = $repair;
        $this->fields = $fields;

     
        $this->created_by = $this->repair->requester_name;
        $this->name  =  $this->repair->requester_name;
        $this->country    = $this->repair->country;
        $this->company    = $this->repair->company_name;
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
                    ->subject($subject . ' (R'. $this->repair->id .')')
                    ->view('emails/repair/updates')
                    ->with([

                        'created_by' => $this->created_by, 
                        'firstname' => $this->name,
                        'country' => $this->country,
                        'company' => $this->repair->requester_company, 
                        'fields' => $this->fields,
                        'rma_no' => $this->repair->id,

                    ]);
    }
}
