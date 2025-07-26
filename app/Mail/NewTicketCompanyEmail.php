<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Option;
use DB;

class NewTicketCompanyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $repair;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair)
    {
        //
        $this->repair = $repair;
        $this->created_by = DB::table('users')->where('id', $this->repair->user_id)->value('firstname');
        $this->firstname  = DB::table('users')->where('id', $this->repair->user_id)->value('firstname');
        $this->lastname   = DB::table('users')->where('id', $this->repair->user_id)->value('lastname');
        $this->country    = DB::table('users')->where('id', $this->repair->user_id)->value('country');
        $this->company    = DB::table('users')->where('id', $this->repair->user_id)->value('company');
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject =Option::where('key','new_ticket_customer_subject' )->value('value'); 

        return $this->from('support@pacom.com')
                    ->subject($subject . ' (Ticket R'. $this->repair->id .')')
                    ->view('emails/repair/new/customer')
                    ->with([
                        'created_by' => $this->created_by, 
                        'firstname' => $this->firstname, 
                        'lastname' => $this->lastname,
                        'country' => $this->country,
                        'company' => $this->repair->company, 
                        'rma_no' => $this->repair->id,

                    ]);
    }
}
