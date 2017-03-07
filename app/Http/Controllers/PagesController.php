<?php

namespace App\Http\Controllers;

use App\Faq;
use App\User;
use App\Clerk;
use App\Point;
use App\Company;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\Request;
use App\Jobs\ChangeClerkPassword;
use Illuminate\Support\Facades\Log;

class PagesController extends Controller
{
    public function home()
    {        
    	if( auth()->check() ) {
    		return redirect()->to('posts');
    	}

    	return view('public.home');
    }

    public function company()
    {
        return view('public.about.company');
    }
    
    public function faqs()
    {
        $faqs = Faq::latest()->get();
        return view('public.about.faqs', compact('faqs'));
    }   

    public function terms()
    {
        $company = Company::first();
        return view('public.about.terms', compact('company'));
    } 

    public function privacy()
    {
        $company = Company::first();
        return view('public.about.privacy', compact('company'));
    }

    public function generateUuid()
    {
        return Uuid::uuid1()->toString();
    }
}
