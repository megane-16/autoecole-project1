<?php

namespace App\Mail;

use App\Models\RegistrationRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RegistrationRequest $request
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Information concernant votre demande d\'inscription DriveFlow',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration_rejected',
        );
    }
}
