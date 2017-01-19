<?php

namespace App\Events\Reservation;

use App\Outlet;
use App\Reservation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ReservationWasConfirmed
{
    use InteractsWithSockets, SerializesModels;

    public $outlet;
    public $reservation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Outlet $outlet, Reservation $reservation)
    {
        $this->outlet = $outlet;
        $this->reservation = $reservation;
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
