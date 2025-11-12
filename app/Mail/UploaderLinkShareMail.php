<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploaderLinkShareMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $albumName;
    public string $uploaderUrl;
    public ?string $passcode;

    /**
     * Create a new message instance.
     */
    public function __construct(string $albumName, string $uploaderUrl, ?string $passcode = null)
    {
        $this->albumName = $albumName;
        $this->uploaderUrl = $uploaderUrl;
        $this->passcode = $passcode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Uploader Link for album ' . $this->albumName,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'faceFinder.components.emails.uploaderLinkShareMail',
            with: [
                'albumName' => $this->albumName,
                'uploaderUrl' => $this->uploaderUrl,
                'passcode' => $this->passcode,
            ],
        );
    }
}
