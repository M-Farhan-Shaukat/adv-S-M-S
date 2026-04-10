<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $recipientName,
        public string $email,
        public string $password,
        public string $role,        // 'Teacher', 'Student', 'Parent'
        public string $schoolName,
        public string $loginUrl,
        public ?string $portalNote = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Your {$this->role} Account - {$this->schoolName}"
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.welcome_credentials');
    }
}
