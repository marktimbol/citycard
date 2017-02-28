<?php

Route::group([
	'domain' => merchantPath(),
], function () {
	Route::get('/', 'Merchants\DashboardController@index');
});

Route::group(['prefix' => 'merchants', 'as' => 'merchants.', 'middleware' => 'auth:merchant'], function() {

	Route::get('/', 'Merchants\DashboardController@index');

	Route::resource('posts', 'Merchants\MerchantPostsController', [
		'only'	=> ['index', 'create', 'store']
	]);
});