<?php

namespace App\Http\Controllers\Merchants;

use App\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerchantsController extends Controller
{
    public function show(Merchant $merchant)
    {
    	$merchant->load('categories');
    	
    	return view('merchants.show', compact('merchant'));
    }
}
