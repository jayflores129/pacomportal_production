<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use DB;
use App\Models\Option;

class RepairItemAdded extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    protected $created_by;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair)
    {
        $this->repair = $repair;
        $this->rmaID = $repair->rma_id;
        $this->firstname  = DB::table('rma_tickets')->where('id', $this->rmaID)->value('requester_name');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        // $subject = Option::where('key','new_ticket_admin_subject' )->value('value'); 
      

        return $this->from('support@pacom.com')
                    ->subject('Ticket R'. $this->rmaID .'  has added the fault item R' . $this->repair->id . '' )
                    ->view('emails/repair/itemadded')
                    ->with([
                        'repair' => $this->repair, 
                        'firstname' => $this->firstname, 
                        'rma_no' => $this->rmaID,
                    ]);
    }
}
