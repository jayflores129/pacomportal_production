<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Option;

class TaskAttachmentMail extends Mailable
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

        $subject = Option::where('key','task_attachment_customer_subject' )->value('value'); 
        $id = $this->user->id;
        $task_id = $this->user->task_id;

        return $this
                ->to($this->user->email)
                ->subject( $subject . ''. $task_id )
                ->view('emails/task/status')
                ->with([
                        'status' => $this->user->status, 
                        'id' => $id,
                        'task_id'=> $this->user->task_id
                ]);
    }
}
