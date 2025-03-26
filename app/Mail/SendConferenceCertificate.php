<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class SendConferenceCertificate extends Mailable
{
    use Queueable, SerializesModels;
    protected $pdfContent;
    protected $payload;

    /**
     * Create a new message instance.
     */
    public function __construct($pdfContent, $payload)
    {
        $this->pdfContent = $pdfContent;
        $this->payload = $payload;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Environment Institute of Kenya Annual Conference Certificate',
            from: new Address('info@eik.co.ke', 'EIK'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.DownloadConferenceCertificate',
            with: [
                'name' => $this->payload['name'],
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
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->payload['name'].'.pdf')->withMime('application/pdf'),
        ];
    }
}
