<?php

namespace App\Transformers;

use Illuminate\Database\Eloquent\Model;
use Themsaid\Transformers\AbstractTransformer;

class PhotosTransformer extends AbstractTransformer
{
    public function transformModel(Model $item)
    {
    	return [
    		'id'	=> $item->id,
    		'url'	=> $item->url,
    	];
    }
}