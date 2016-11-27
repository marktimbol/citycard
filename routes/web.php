<?php

use App\Merchant;
use Maatwebsite\Excel\Excel;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    $location = Location::get();
    dd($location);
    
    return view('welcome');
});

Route::get('/import', function() {
	$excel = App::make('excel');

    $excel->load('merchants.xls', function($reader) {
    	$merchants = $reader->all();
    	foreach( $merchants as $merchant )
    	{
    		$merchant = Merchant::create([
    			'name'	=> $merchant->name,
    			'phone'	=> $merchant->phone,
    			'city'	=> $merchant->city,
    			'country'	=> $merchant->country,
    			'email'	=> $merchant->email,
    			'password'	=> bcrypt($merchant->password)
    		]);

            $merchant->outlets()->create([
                'name'  => $merchant->name,
                'email'  => $merchant->email,
                'password'  => $merchant->email,
                'phone'  => $merchant->phone,
                'address1'  => '',
                'address2'  => '',
                'latitude'  => '',
                'longitude'  => '',
                'type'  => '',
                'country'  => $merchant->country,
                'city'  => $merchant->city,
                'area'  => '',
            ]);
    	}

    	return 'Done';
    })->get();
});

Auth::routes();

Route::get('/home', 'HomeController@index');
