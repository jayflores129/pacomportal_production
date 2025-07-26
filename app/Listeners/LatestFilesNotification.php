<?php

namespace App\Listeners;

use App\Events\LatestFiles;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\LatestFilesMail;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class LatestFilesNotification
{

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
     * @param  LatestFiles  $event
     * @return void
     */
    public function handle(LatestFiles $event)
    {
        
        $files       = $event->files;
        $subscribers = DB::table('users')->where('subscribe', 1)->get();

        // $subscribers = DB::table('users')->where('notify_technical_doc', 1)->orWhere('notify_firmware', 1)->get();

        if( $subscribers ) {
            foreach($subscribers as $subscribe) {

                $firstname = $subscribe->firstname;
                 //Send assignee a notification email

                if ($event->documentType == 'technical' && $subscribe->notify_technical_doc == 1) {
                    Mail::to($subscribe->email)->send( New LatestFilesMail($files, $firstname) );
                }

                if ($event->documentType == 'software' && $subscribe->notify_firmware == 1) {
                    Mail::to($subscribe->email)->send( New LatestFilesMail($files, $firstname) );
                }

            }
        }

    }
}
