<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Ticket Created: ' . $this->ticket->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.new-ticket',
            with: [
                'ticket' => $this->ticket,
                'editUrl' => route('tickets.edit', $this->ticket),
                'viewUrl' => route('tickets.show', $this->ticket),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
