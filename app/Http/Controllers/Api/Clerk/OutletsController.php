<?php

namespace App\Http\Controllers\Api\Clerk;

use App\Outlet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\OutletTransformer;

class OutletsController extends Controller
{
    public function index()
    {
        $clerk = auth()->guard('clerk_api')->user();
        $clerkOutlets = $clerk->outlets;
        $clerkOutlets->load('reservations');

        return response()->json([
            'status'    => 1,
            'data'  => [
                'outlets'    => OutletTransformer::transform($clerkOutlets)
            ]
        ]);
    }

    public function show(Outlet $outlet)
    {
        $clerk = auth()->guard('clerk_api')->user();

        if( $clerk->isInOutletRange($outlet, request()->lat, request()->lng) )
        {
            $outlet->load('posts', 'reservations', 'photos');

            return response()->json([
                'status'    => 1,
                'message'   => 'Access to the Outlet was accepted.',
                'data'  => [
                    'outlet'    => OutletTransformer::transform($outlet)
                ]
            ]);
        }

        return response()->json([
            'status'    => 0,
            'message'   => 'You are far away from the Outlet.',
            'data'  => [
                'outlet'    => []
            ]
        ]);

    }
}
