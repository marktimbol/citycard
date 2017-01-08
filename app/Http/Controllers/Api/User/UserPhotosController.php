<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserPhotosController extends Controller
{
    public function store(Request $request)
    {
    	return $request->all();
    }
}
