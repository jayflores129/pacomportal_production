<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use App\Models\Option;
use Illuminate\Support\Facades\DB;

class RepairQuotation extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    protected $created_by;

    protected $_subject = null;
    protected $repair_status = null;
    protected $receiver_email = null;
    protected $firstname = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair, $_subject = null, $repair_status = null, $receiver_email = null)
    {
        //
        $this->repair = $repair;
        $this->_subject = $_subject;
        $this->repair_status = $repair_status;

        // $this->created_by = DB::table('users')->where('id', $this->repair->user_id)->value('firstname');
        $this->firstname  = DB::table('users')->where('id', $this->repair->user_id)->value('firstname');

        if ($receiver_email) {
            $this->firstname  = DB::table('users')->where('email', $receiver_email)->value('firstname');
        }

        // $this->lastname   = DB::table('users')->where('id', $this->repair->user_id)->value('lastname');
        // $this->country    = DB::table('users')->where('id', $this->repair->user_id)->value('country');
        // $this->company    = DB::table('users')->where('id', $this->repair->user_id)->value('company');
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
                    ->subject(
                        $this->_subject ?? 'To be confirmed - New quotation for R'. $this->repair->id
                    )
                    ->view('emails/repair/quotation')
                    ->with([
                        'repair_status' => $this->repair_status,
                        'firstname' => $this->firstname, 
                        'rma_no' => $this->repair->id,

                    ]);
    }
}
