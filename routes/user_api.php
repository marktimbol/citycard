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
	Route::resource('clerks.messages', 'Api\User\MessagesController', [
		'only'	=> ['store']
	]);
	Route::put('user/email', 'Api\User\UpdateEmailController@update');
	Route::put('user/mobile', 'Api\User\UpdateMobileController@update');
	Route::put('user/change-password', 'Api\User\ChangePasswordController@update');
	Route::post('user/photo', 'Api\User\UserPhotosController@store');
	Route::put('user/profile', 'Api\User\ProfileController@update');
	Route::post('user/invites', 'Api\User\InvitesController@store');

	// User reservations list
	Route::get('user/reservations', 'Api\User\ReservationsController@index');
	// List all the outlet items available for reservation
	Route::get('outlets/{outlet}/reservations', 'Api\Outlet\OutletReservationsController@index');
	// User reserve a services from the outlet
	Route::post('outlets/{outlet}/reservations', 'Api\User\OutletReservationsController@store');
	// User cancels a reservation
	Route::delete('outlets/{outlet}/reservations/{reservation}/cancel', 'Api\User\CancelReservationsController@destroy');

	// User follows an outlet
	Route::post('outlets/{outlet}/follows', 'Api\User\OutletFollowersController@store');
	// User unfollows an outlet
	Route::delete('outlets/{outlet}/unfollow', 'Api\User\OutletFollowersController@destroy');
});