<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        switch( $this->user->type )
        {
            case 'submitter':
                $subject = '[Pacom Update] New changes on the task you submitted!';
                break;
            case 'new_assignee':
                $subject = '[Pacom Update] There is new task for you';
                break;
            case 'old_assignee':
                $subject = '[Pacom Update] New changes on the task you handled';
                break;
            default:
                $subject = '[Pacom Update] New changes on the task';
                break;                 
        }


        return $this
                ->to($this->user->email)
                ->subject( $subject )
                ->view('emails/task/status')
                ->with([

                        'status' => $this->user->status, 

                ]);
    }
}
