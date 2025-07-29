<?php

namespace App\Listeners;

use App\Events\NewTicket;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\NewRepairNotify;
use App\Mail\RepairNotification;
use App\Mail\NewTicketCompanyEmail;
use App\Mail\NewRepairTicketMail;
use Mail;
use App\Models\Option;
use Auth;
use App\Models\User;
use DB;

class NewTicketNotification
{
     protected $repair;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewTicket  $event
     * @return void
     */
    public function handle(NewTicket $event)
    {
        $this->repair = $event->repair;

        $admin_email = Option::where('key', 'notification_new_repair_email')->value('value');

        //Email Ticket Submitter
        //Mail::to(Auth::user()->email)->send(new NewRepairTicketMail($this->repair));

        // Email Admin about the new ticket
        try {
            Mail::to($admin_email)->send(new RepairNotification($this->repair));
            Mail::to('paulz@spgcontrols.com')->send(new RepairNotification($this->repair));

            // Send to Assignee
            $assignee = User::find($this->repair->assign_id)->email;
            Mail::to( $assignee )->send(new NewTicketCompanyEmail($this->repair));
        }
        catch (\Exception $e) {}
       

        $company_email = DB::table('companies')->where('name', 'LIKE', '%' .$this->repair->company . '%')->value('email');
        if($company_email) {
            // Notify Company
            try {
                Mail::to($company_email)->send(new NewTicketCompanyEmail($this->repair));
            }
            catch (\Exception $e) {}
        }

    }
}
