<?php

/*
|--------------------------------------------------------------------------
| Clerk API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'prefix' => 'clerk', 'middleware' => 'auth:clerk_api'], function() {
	Route::resource('users.messages', 'Api\Clerk\MessagesController', [
		'only'	=> ['store']
	]);

	Route::get('outlets/{outlet}', 'Api\Clerk\OutletsController@show');	
	Route::post('outlets/{outlet}/items-for-reservation', 'Api\Clerk\OutletItemsForReservationController@store');	
	// Get all the reservations of an outlet
	Route::get('outlets/{outlet}/reservations', 'Api\Clerk\OutletReservationsController@index');	
	// View an outlet reservation
	Route::get('outlets/{outlet}/reservations/{reservation}', 'Api\Clerk\OutletReservationsController@show');
	// Confirm a reservation
	Route::put('reservations/{reservation}/confirm', 'Api\Clerk\ConfirmReservationsController@update');	
});