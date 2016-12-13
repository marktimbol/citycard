<?php

Route::group([
	'domain' => merchantPath(),
], function () {
	Route::get('/', function() {
		return 'Hello from Merchant';
	});
	// Route::group(['as' => 'api.', 'prefix' => 'api'], function() {
	// 	// Merchant Authentication
	// 	Route::post('login', 'Api\Auth\Merchant\LoginController@login');	
	// });

	// Merchant Authentication Routes...
	// Route::get('/merchants/login', 'Auth\Merchants\LoginController@showLoginForm');
	// Route::post('/merchants/login', 'Auth\Merchants\LoginController@login');
	// Route::delete('/merchants/logout', 'Auth\Merchants\LoginController@logout');
});

Route::group(['prefix' => 'merchants', 'as' => 'merchants.', 'middleware' => 'auth:merchant'], function() {

	Route::get('/', function() {
		return 'Merchant Dashboard';
	});

	Route::resource('posts', 'Merchants\MerchantPostsController');
});