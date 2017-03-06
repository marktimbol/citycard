<?php

namespace App\Http\Controllers;

use App\Faq;
use App\User;
use App\Clerk;
use App\Point;
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

    public function givePoints()
    {
        Point::create([
            'registration'  => 1000,
        ]);
        
        $registration_points = Point::first()->registration;
        $description = sprintf('You received %s points upon registration.', $registration_points);

        User::chunk(30, function($users) use ($description, $registration_points) {
            foreach( $users as $user ) {
                $user->makeTransaction('credit', $description, $registration_points);
            }
        });

        return 'Done';
    }
}
