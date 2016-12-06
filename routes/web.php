<?php


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
    return view('welcome');
});

Route::get('/qb', function() {

	$client = new GuzzleHttp\Client([
		'headers'	=> [
			'QB-Token'	=> env('QB_Token'),
		]
	]);

	$response = $client->post('https://api.quickblox.com/users.json', [
        'user[login]'   => 'john@doe.com',
        'user[password]'   => 'johndoe',
        'user[email]'   => 'john@doe.com',
	]);

	dd($response);
});	


Auth::routes();
Route::get('/register/confirm/{token}', 'Auth\ConfirmEmailController@confirm');

Route::get('/home', 'HomeController@index');
