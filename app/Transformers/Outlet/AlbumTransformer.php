<?php

namespace App\Transformers\Outlet;

use App\Transformers\PhotoTransformer;
use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class AlbumTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	$output = array_only($item->toArray(), [
    		'id', 'title'
    	]);

        $output['photos_count'] = $item->photos()->count();
        $output['photo'] = $item->photos()->inRandomOrder()->first()->url;

    	return $output;	
    }
}