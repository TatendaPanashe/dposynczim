<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectBriefSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  array<string, mixed>  $brief
     * @param  array<int, UploadedFile>  $images
     */
    public function __construct(
        public array $brief,
        public array $images = [],
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [$this->brief['email']],
            subject: 'New project brief from '.$this->brief['name'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.project-brief',
            text: 'emails.project-brief-text',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return array_map(
            fn (UploadedFile $image): Attachment => Attachment::fromPath($image->getRealPath())
                ->as($image->getClientOriginalName())
                ->withMime($image->getMimeType() ?? 'application/octet-stream'),
            $this->images,
        );
    }
}
