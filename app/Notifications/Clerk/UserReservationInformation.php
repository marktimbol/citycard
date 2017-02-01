<?php

namespace App\Notifications\Clerk;

use App\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class UserReservationInformation extends Notification
{
    use Queueable;

    public $reservation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [PusherChannel::class];
    }

    /**
     * Get the iOS push notification representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toPushNotification($notifiable)
    {
        $body = sprintf('%s reserved %s for %s people on %s at %s', 
            $this->reservation->user->name,
            $this->reservation->option,
            $this->reservation->quantity,
            $this->reservation->date,
            $this->reservation->time,
        );

        return PusherMessage::create()
                ->iOS()
                ->badge(1)
                ->sound('success')
                ->title('CityCard')
                ->body($body);
    }
}
