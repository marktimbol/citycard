<?php

namespace App\Http\Controllers;

use App\Faq;
use App\Clerk;
use App\Company;
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

    public function updateClerkPassword()
    {
        Clerk::chunk(50, function($clerks) {        
            foreach( $clerks as $clerk )
            {
                $clerk->password = 'citycard';
                $clerk->save();
            }
        });

        // dispatch(new ChangeClerkPassword($action));

        return 'Done';
    }         

}
