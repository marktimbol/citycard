<?php

namespace App\Policies;

use App\Outlet;
use App\Reservation;
use Illuminate\Auth\Access\HandlesAuthorization;

class OutletReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the outlet can view the reservation.
     *
     * @param  \App\Outlet  $outlet
     * @param  \App\Reservation  $reservation
     * @return mixed
     */
    public function view(Outlet $outlet, Reservation $reservation)
    {
        dd($outlet, $reservation);
        return $outlet->reservations()->contains($reservation);
    }

    /**
     * Determine whether the outlet can create reservations.
     *
     * @param  \App\Outlet  $Outlet
     * @return mixed
     */
    public function create(Outlet $outlet)
    {
        //
    }

    /**
     * Determine whether the outlet can update the reservation.
     *
     * @param  \App\Outlet  $Outlet
     * @param  \App\Reservation  $reservation
     * @return mixed
     */
    public function update(Outlet $outlet, Reservation $reservation)
    {
        return $outlet->reservations()->contains($reservation);
    }

    /**
     * Determine whether the Outlet can delete the reservation.
     *
     * @param  \App\Outlet  $Outlet
     * @param  \App\Reservation  $reservation
     * @return mixed
     */
    public function delete(Outlet $outlet, Reservation $reservation)
    {
        return $outlet->reservations()->contains($reservation);
    }
}
