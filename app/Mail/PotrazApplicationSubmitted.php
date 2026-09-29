<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PotrazApplicationSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $documentType,
        public string $subjectName,
        public string $filename,
        public string $pdfContent,
        public string $senderName,
        public string $senderEmail,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->senderEmail],
            subject: $this->documentType.' application - '.$this->subjectName,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.potraz-application',
            text: 'emails.potraz-application-text',
        );
    }

    /** @return array<int, Attachment> */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn (): string => $this->pdfContent, $this->filename)
                ->withMime('application/pdf'),
        ];
    }
}
