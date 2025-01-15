<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerActivityEmail extends Mailable
{
    use Queueable, SerializesModels;

    // //variabel
    // public $subject; // judul di emailnya
    // public $body; // body

    public function __construct()
    {
        // $this->subject = $subject;
        // $this->body = $body;
    }

    public function build()
    {
       return $this->from($address = 'notif@shelter.co.id', $name = 'CAIS Shelter')
                    ->subject('TESSSS')
                    ->view('email.customer-activity-email')
                    ->with([]);
    }
    // /**
    //  * Get the message envelope.
    // //  */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //     );
    // }

    // /**
    //  * Get the message content definition.
    //  */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    // /**
    //  * Get the attachments for the message.
    //  *
    //  * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    //  */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
