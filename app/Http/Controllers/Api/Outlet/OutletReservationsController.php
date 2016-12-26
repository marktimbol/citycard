<?php

namespace App\Http\Controllers\Api\Outlet;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ItemsForReservationTransformer;

class OutletReservationsController extends Controller
{
    public function index(Outlet $outlet)
    {
        $outlet->load('itemsForReservation');

        return ItemsForReservationTransformer::transform($outlet->itemsForReservation);
    }
}
