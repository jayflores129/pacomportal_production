<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RepairStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $repair;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repair)
    {
        $this->repair = $repair;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this
                ->to($this->repair->email)
                ->subject('Pacom - Update to R' . $this->repair->id )
                ->view('emails/repair/status')
                ->with([


                        'firstname' => $this->repair->firstname, 
                        'repair_id' => $this->repair->id

                ]);
    }
}
