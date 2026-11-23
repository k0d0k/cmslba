<?php

namespace Modules\Pelanggan\Mail;

use Modules\User\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email berisi password baru untuk akun pelanggan.
 */
class PelangganPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.pelanggan_password_reset_subject'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.pelanggan-password-reset');
    }
}
