<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Option;

class LatestFilesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $firstname;
    public $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($files, $firstname)
    {
        $this->files = $files;
        $this->firstname = $firstname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject1  = Option::where('key','new_file_customer_subject1' )->value('value');
        $subject2  = Option::where('key','new_file_customer_subject2' )->value('value');
        $subject3  = Option::where('key','new_file_customer_subject3' )->value('value');


        if($this->files->type == 1) {
            $subject   = $subject1;
            $filetype  = 'software / firmware release';
            $filelink  = url('firmwares');
        } elseif ( $this->files->type == 2 ) {
            $subject   = $subject2;
            $filetype  = 'technical document';
            $filelink  = url('technical-documentation');
        } elseif(  $this->files->type == 3 ) {
            $subject   = $subject3;
            $filetype  = 'certificate';
            $filelink  = url('certificates');
        } else {
            $subject   = '[NOTIFICATION] Pacom - New file has been added';
            $filetype  = 'File';
            $filelink  = '';
        }

        return $this->from('support@pacom.com')
                    ->subject($subject)
                    ->view('emails/files/new')
                    ->with([
                        'filetype' => $filetype,
                        'filename' => $this->files->name,
                        'link' => $filelink,
                        'name' => $this->firstname

                    ]);;
                  
    }

}
