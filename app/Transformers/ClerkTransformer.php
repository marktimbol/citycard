<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class ClerkTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
        $output = array_only($item->toArray(), [
            'id', 'uuid', 'first_name', 'last_name', 'email', 'phone', 'photo',
        ]);    

        // Get clerk profile
        if( auth()->guard('clerk')->check() ) {
            $clerk = auth()->guard('clerk')->user();

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

        $output['online'] = 1;

        if( $this->isRelationshipLoaded($item, 'outlets') ) {
            $output['outlets'] = OutletTransformer::transform($item->outlets);
        }         

        return $output;
    }
}