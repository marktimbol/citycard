<?php

namespace App\Notifications\User;

use App\Outlet;
use App\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class ConfirmedReservation extends Notification
{

    public $outlet;
    public $reservation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Outlet $outlet, Reservation $reservation)
    {
        $this->outlet = $outlet;
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
     * Get the iOS push notification representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toPushNotification($notifiable)
    {
        // $body = sprintf('%s reservation was confirmed', $notifiable->item->title);
        $body = sprintf('%s: Your reservation was confirmed.', $this->outlet->name);

        return PusherMessage::create()
                ->iOS()
                ->badge(1)
                ->sound('success')
                ->title('CityCard')
                ->body($body)
                ->setOption('type', 'reservation.confirmed');
    }
}
