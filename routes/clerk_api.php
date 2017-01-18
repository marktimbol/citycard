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

	Route::put('change-password', 'Api\Auth\Clerk\ChangePasswordController@update');

	Route::resource('users.messages', 'Api\Clerk\MessagesController', [
		'only'	=> ['store']
	]);

	Route::get('outlets', 'Api\Clerk\OutletsController@index');
	Route::get('outlets/{outlet}', 'Api\Clerk\OutletsController@show');

	// Create an item for reservation
	Route::post('outlets/{outlet}/items-for-reservation', 'Api\Clerk\OutletItemsForReservationController@store');

	// Confirm user's reservation
	Route::put('/outlets/{outlet}/reservations/{reservation}/confirm', 'Api\Clerk\ConfirmUserReservationController@update');
	
	Route::get('/outlets/{outlet}/reservations/cancelled', 'Api\Clerk\OutletCancelledReservationsController@index');
	
	// Outlet Reservations
	Route::resource('outlets.reservations', 'Api\Clerk\OutletReservationsController', [
		'only'	=> ['index', 'show']
	]);	

	// User reservation actions (modify and cancel)
	Route::resource('outlets.reservations', 'Api\Clerk\UserReservationsController', [
		'only'	=> ['update', 'destroy']
	]);	

	Route::get('users/{user}', 'Api\Clerk\UsersController@show');
});