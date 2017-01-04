<?php

namespace App\Http\Controllers\Dashboard;

use App\Faq;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FAQsController extends Controller
{
    public function index()
    {
    	$faqs = Faq::all();

    	return view('dashboard.faqs.index', compact('faqs'));
    }

    public function show(Faq $faq)
    {
        return view('dashboard.faqs.show', compact('faq'));
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'question'	=> 'required|min:3',
    		'answer'	=> 'required|min:3'
    	]);

    	$faq = Faq::create([
    		'question'	=> $request->question,
    		'answer'	=> $request->answer,
    	]);

    	flash()->success('FAQ has been successfully saved.');
    	return redirect()->route('dashboard.faqs.index');
    }

    public function edit(Faq $faq)
    {
        return view('dashboard.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $this->validate($request, [
            'question'  => 'required|min:3',
            'answer'    => 'required|min:3'
        ]);

        $faq->update($request->all());

        flash()->success('FAQ has been successfully updated.');
        return redirect()->route('dashboard.faqs.show', $faq->id);      
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        flash()->success('FAQ has been successfully removed.');
        return redirect()->route('dashboard.faqs.index');        
    }
}
