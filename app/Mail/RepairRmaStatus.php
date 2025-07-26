<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Option;

class RepairRmaStatus extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    protected $created_by;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($fields)
    {

        $this->fields = $fields;
        $this->name =  $this->fields['fullname']; 
        $this->status =  $this->fields['status']; 
        $this->rmaID =  $this->fields['rma_id']; 
        $this->date =  $this->fields['date']; 
        $this->courier =  $this->fields['courier']; 
        $this->consignment_note =  $this->fields['consignment_note']; 

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
                    ->subject('New status for R'. $this->fields['rma_id'] . '.')
                    ->view('emails/repair/Rmastatus')
                    ->with([

                        'firstname' => $this->name,
                        'status' => $this->status,
                        'date' => $this->date,
                        'consignment_note' => $this->consignment_note,
                        'courier' => $this->courier,
                        'rmaID' =>  $this->rmaID

                    ]);
    }
}
