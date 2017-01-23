<?php

namespace App\Http\Controllers;

use App\Outlet;
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

    public function explore()
    {
        $outlets = Outlet::with(['merchant', 'posts' => function($query) {
            return $query->with('photos')->latest()->take(3)->get();
        }])->latest()->take(10)->get();

        // dd($outlets->toArray());

        return view('public.explore', compact('outlets'));
    }

    public function company()
    {
        return view('public.about.company');
    }
    
    public function faqs()
    {
        return view('public.about.faqs');
    }                 
}
