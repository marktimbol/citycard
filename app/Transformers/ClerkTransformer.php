<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ClerkTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'first_name', 'last_name', 'display_name', 'email', 'phone', 'photo', 'is_online'
        ]);

        if( auth()->guard('user_api')->check() ) {
            $output['uuid'] = $item->uuid;
        }

        // Get clerk profile
        if( auth()->guard('clerk')->check() ) {
            $clerk = auth()->guard('clerk')->user();

            $output['uuid'] = $item->uuid;

            // Temporary only
            $output['permissions'] = [
                'messaging'   => true,
                'transactions'   => true,
                'view_reservations'   => true,
                'manage_reservations'   => true,
                'manage_photo_albums'   => true,
                'manage_shop_fronts'   => true,
                'manage_menus'   => true,
            ];

            $output['api_token'] =$clerk->api_token;
        } 

        // Get clerk profile
        if( auth()->guard('clerk_api')->check() ) {
            $clerk = auth()->guard('clerk_api')->user();

            $output['uuid'] = $item->uuid;

            // Temporary only
            $output['permissions'] = [
                'messaging'   => true,
                'transactions'   => true,
                'view_reservations'   => true,
                'manage_reservations'   => true,
                'manage_photo_albums'   => true,
                'manage_shop_fronts'   => true,
                'manage_menus'   => true,
            ];

            $output['api_token'] = $clerk->api_token;
        } 

        if( $this->isRelationshipLoaded($item, 'outlets') ) {
            $output['outlets'] = OutletTransformer::transform($item->outlets);
        }         

        return $output;
    }
}