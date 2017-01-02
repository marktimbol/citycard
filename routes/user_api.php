<?php

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['as' => 'api.', 'middleware' => 'auth:user_api'], function() {
	Route::resource('clerks.messages', 'Api\User\MessagesController');
	Route::put('user/email', 'Api\User\UpdateEmailController@update');
	Route::put('user/mobile', 'Api\User\UpdateMobileController@update');
	Route::put('user/change-password', 'Api\User\ChangePasswordController@update');
	Route::put('user/profile', 'Api\User\ProfileController@update');

	// User reservations list
	Route::get('user/reservations', 'Api\User\ReservationsController@index');

	// List all the outlet items available for reservation
	Route::get('outlets/{outlet}/reservations', 'Api\Outlet\OutletReservationsController@index');
	// User reserve a services from the outlet
	Route::post('outlets/{outlet}/reservations', 'Api\User\OutletReservationsController@store');
	// User follows an outlet
	Route::post('outlets/{outlet}/follows', 'Api\User\OutletFollowersController@store');
	// User unfollows an outlet
	Route::delete('outlets/{outlet}/unfollow', 'Api\User\OutletFollowersController@destroy');
});