<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\PusherPushNotifications\PusherChannel;
use NotificationChannels\PusherPushNotifications\PusherMessage;

class ConfirmedReservation extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $reservation = $notifiable->reservation->load('outlets');
        $outlet = $reservation->outlets()->first();

        // $body = sprintf('%s reservation was confirmed', $notifiable->item->title);
        $body = sprintf('%s: Your reservation was confirmed.', $outlet->name);

        return PusherMessage::create()
                ->iOS()
                ->badge(1)
                ->sound('success')
                ->title('CityCard')
                ->body($body);
    }
}
