<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Option;

class CommentNotification extends Mailable
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
        $ticket_id = $this->user->ticket_id;
        $subject   = Option::where('key','task_comment_customer_subject' )->value('value');

        return $this
                ->from('support@pacom.com')
                ->to($this->user->email)
                ->subject($subject . ' # '.$ticket_id .'')
                ->view('emails/repair/comment')
                ->with([
                        'ticket' => $ticket_id,
                ]);

    }
}
