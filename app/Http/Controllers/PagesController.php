<?php

namespace App\Http\Controllers;

use App\User;
use App\Clerk;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function home()
    {
    	if( auth()->check() ) {
    		return redirect()->to('posts');
    	}

    	return view('public.home');
    }

    public function about()
    {
    	return view('public.about');
    }

    public function events()
    {
        return view('public.coming-soon');
    }   

    public function directory()
    {
        return view('public.coming-soon');
    }

    public function merchants()
    {
        return view('public.coming-soon');
    }

    public function support()
    {
        return view('public.coming-soon');
    }                   
}
