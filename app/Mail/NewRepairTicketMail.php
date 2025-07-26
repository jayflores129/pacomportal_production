<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class NewRepairTicketMail extends Mailable
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
        return $this->from('support@pacom.com')
                    ->subject('[NOTIFICATION] Pacom - New ticket you have added (Ticket R'. $this->repair->id .')')
                    ->view('emails/repair-notification')
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
