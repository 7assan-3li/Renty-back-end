<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $type;
    protected $bookingId;

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $message
     * @param string $type (pending, confirmation, reminder, expired)
     * @param int|null $bookingId
     */
    public function __construct($title, $message, $type, $bookingId = null)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->bookingId = $bookingId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'booking_id' => $this->bookingId,
        ];
    }
}
