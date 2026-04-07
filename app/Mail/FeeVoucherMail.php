<?php

namespace App\Mail;

use App\Models\FeeVoucher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeeVoucherMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FeeVoucher $voucher) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Fee Voucher - ' . now()->format('F Y'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.fee_voucher');
    }
}
