<?php

Route::group([
	'domain' => merchantPath(),
], function () {
	Route::get('/', function() {
		return 'Hello from Merchant';
	});
});

Route::group(['prefix' => 'merchants', 'as' => 'merchants.', 'middleware' => 'auth:merchant'], function() {

	Route::get('/', function() {
		return 'Merchant Dashboard';
	});

	Route::resource('posts', 'Merchants\MerchantPostsController', [
		'only'	=> ['index', 'create', 'store']
	]);
});