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

Route::get('/', ['as' => 'home', 'uses' => 'PagesController@home']);
Route::get('/posts', ['as' => 'posts.index', 'uses' => 'PostsController@index']);

Route::get('/qb', function() {

	$client = new GuzzleHttp\Client([
		'headers'	=> [
			'QB-Token'	=> env('QB_Token'),
		]
	]);

	$response = $client->post('https://api.quickblox.com/users.json', [
		'form_params'	=> [
	        'user[login]'   => 'jane@doe.com',
	        'user[full_name]'   => 'Jane Doe',
	        'user[phone]'   => '+971568207189',
	        'user[password]'   => 'johndoe1234',
	        'user[email]'   => 'jane@doe.com',
		]
	]);

	dd($response);
});	


Auth::routes();
Route::get('/register/confirm/{token}', 'Auth\ConfirmEmailController@confirm');

Route::get('/home', 'HomeController@index');
