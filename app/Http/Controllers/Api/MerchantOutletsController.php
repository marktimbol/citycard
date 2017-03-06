<?php

namespace App\Http\Controllers\Api;

use App\Merchant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MerchantOutletsController extends Controller
{
    public function index(Merchant $merchant)
    {
    	$merchant->load('outlets:id,merchant_id,name');

    	return $merchant->outlets;
    }
}
