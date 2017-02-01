<?php

namespace App\Events\User;

use App\Outlet;
use App\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class UserReservedAnItem
{
    use InteractsWithSockets, SerializesModels;

    public $reservation;

    public $outlet;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Reservation $reservation, Outlet $outlet)
    {
        $this->reservation = $reservation->load('user');
        $this->outlet = $outlet->load('clerks');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
