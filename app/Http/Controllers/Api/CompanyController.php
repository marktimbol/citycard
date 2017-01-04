<?php

namespace App\Http\Controllers\Api;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
    	return Company::first();
    }
}
