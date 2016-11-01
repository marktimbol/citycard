<?php

// Merchant Authentication Routes...
Route::get('/merchants/login', 'Auth\Merchants\LoginController@showLoginForm');
Route::post('/merchants/login', 'Auth\Merchants\LoginController@login');
Route::delete('/merchants/logout', 'Auth\Merchants\LoginController@logout');

// Merchant Registration Routes...
Route::get('/merchants/register', 'Auth\Merchants\RegisterController@showRegistrationForm');
Route::post('/merchants/register', 'Auth\Merchants\RegisterController@register');

Route::group(['prefix' => 'merchants', 'as' => 'merchants.', 'middleware' => 'auth:merchant'], function() {

	Route::get('/', function() {
		return 'Merchant Dashboard';
	});

	Route::resource('posts', 'Merchants\MerchantPostsController');
});