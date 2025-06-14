<?php

namespace App\Notifications;

use App\Models\MeetingSlot;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingBookedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $meeting_slot;

    /**
     * Create a new notification instance.
     */
    public function __construct(MeetingSlot $meeting_slot)
    {
        $this->meeting_slot = $meeting_slot;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'meeting_slot_id' => $this->meeting_slot->id,
            'teacher_id' => $this->meeting_slot->teacher_id, // User id who made the meeting
            'meeting_uuid' => $this->meeting_slot->meeting_uuid, // Will be used as a link to the meeting detail page
            'created_at' => $this->meeting_slot->created_at,
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'meeting-booked';
    }
}
