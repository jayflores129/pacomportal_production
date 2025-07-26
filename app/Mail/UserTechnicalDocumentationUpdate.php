<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserTechnicalDocumentationUpdate extends Mailable
{
    use Queueable, SerializesModels;

    private $file_release, $file;

    /**
     * Create a new message instance.
     */
    public function __construct($file_release, $file)
    {
        $this->file_release = $file_release;
        $this->file = $file;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Technical Documentation Update',
            from: 'support@pacom.com',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails/technical-documentation/update',
            with: [
                'file_release' => $this->file_release,
                'file' => $this->file,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
