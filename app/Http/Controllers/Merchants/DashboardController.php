<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
    	$clerk = auth()->guard('clerk')->user();
    	$clerk->load('merchant.clerks', 'outlets');
    	
		return view('merchants.dashboard', compact('clerk'));
    }
}
