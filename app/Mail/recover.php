<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class recover extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(private string $token)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('info@eik.co.ke', 'EIK Admin'),
            subject: 'Password Recovery',
        );
    }
    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $url = config('app.frontend_url') . '/recover?token=' . $this->token;
        return new Content(
            view: 'emails.recovery',
            with: [
                'title'=>'Password Recovery',
                'body'=>'Click the link below to reset your password. Valid for 1 hour.',
                'url' => $url
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
