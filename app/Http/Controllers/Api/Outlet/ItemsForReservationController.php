<?php

namespace App\Http\Controllers\Api\Outlet;

use App\Outlet;
use App\ItemForReservation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ItemsForReservationTransformer;

class ItemsForReservationController extends Controller
{
    public function index(Outlet $outlet)
    {
        $outlet->load('itemsForReservation');

        return ItemsForReservationTransformer::transform($outlet->itemsForReservation);
    }

    public function show(Outlet $outlet, ItemForReservation $item)
    {
    	return $item->toArray();
    }
}
