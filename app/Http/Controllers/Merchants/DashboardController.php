<?php

namespace App\Http\Controllers\Merchants;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
		return 'Hello from Merchant';
    }
}
