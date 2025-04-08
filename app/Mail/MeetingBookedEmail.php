<?php

namespace App\Mail;

use App\Models\MeetingSlot;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MeetingBookedEmail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    protected MeetingSlot $meeting_slot;
    protected User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(MeetingSlot $meeting_slot, User $user)
    {
        $this->meeting_slot = $meeting_slot;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'support@nlrc.ph'), env('MAIL_FROM_NAME', 'NLRC-ESL')),
            subject: 'NLRC-ESL Meeting Booked!'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.meeting-booked',
            with: [
                'meeting_slot' => $this->meeting_slot,
                'user' => $this->user,
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
