<?php

namespace App\Http\Controllers\Dashboard;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public function index()
    {
    	$company = Company::first();

    	return view('dashboard.company.index', compact('company'));
    }

    public function create()
    {
    	return view('dashboard.company.create');
    }

    public function store(Request $request)
    {
    	Company::create($request->all());

    	flash()->success('Company information has been successfully saved.');
    	return redirect()->route('dashboard.company.index');
    }

    public function update(Request $request, Company $company)
    {
        if( $request->has('only') )
        {
            $company->{$request->only} = $request->info;
            $company->update();
        }

        flash()->success('Company information has been successfully updated.');
        return redirect()->route('dashboard.company.index');  
    }
}
