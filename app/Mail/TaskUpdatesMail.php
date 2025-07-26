<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TaskUpdatesMail extends Mailable
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
        $url = url('admin/softwares') . '/' . $this->user->task_id;
        $resolution = $this->user->resolution;
        $date_resolved = $this->user->date_resolved;

        return $this
                    ->from('support@pacom.com')
                    ->subject('[NOTIFICATION] Pacom - Task #'. $this->user->task_id .' has been resolved.')
                    ->view('emails/task/resolve')
                    ->with([
                        'name' => $this->user->firstname,
                        'task_id' => $this->user->task_id,
                        'url' => $url,
                        'resolution' => $resolution,
                        'time' => $date_resolved,
                        'resolver' => $this->user->resolver,
                    ]);
    }
}
