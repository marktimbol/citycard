<?php

namespace App\Http\Controllers\Dashboard;

use App\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerchantPhotosController extends Controller
{
    public function update(Request $request, Merchant $merchant)
    {
	    $uploadPath = sprintf('merchants/%s/logo', str_slug($merchant->name));
	    $file = $request->file->store($uploadPath, 's3');

        $merchant->logo = $file;
        $merchant->save();

    	return back();
    }
}
