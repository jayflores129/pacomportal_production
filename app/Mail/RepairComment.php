<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class RepairComment extends Mailable
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
        $this->firstname  = $repair->requester_name;
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
                    ->subject('Pacom team has added a new comment to the R' . $this->rmaID . '' )
                    ->view('emails/repair/comment')
                    ->with([
                        'repair' => $this->repair, 
                        'firstname' => $this->firstname, 
                        'rma_no' => $this->rmaID,
                    ]);
    }
}
