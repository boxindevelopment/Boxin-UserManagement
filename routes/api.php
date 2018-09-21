<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'API'], function () {
	Route::post('login', ['uses' => 'AuthController@login', 'as' => 'api.auth.getToken']);
	Route::get('facebook/{token}', ['uses' => 'FacebookController@getToken', 'as' => 'api.facebook']);
	Route::get('google/{token}', ['uses' => 'GoogleController@getToken', 'as' => 'api.google']);
	Route::post('password/email', 'PasswordController@forgotPassword')->name('api.password.email');
	// Route::post('password/reset', 'PasswordController@changePassword')->name('api.password.reset');

	Route::post('sign-up', ['uses' => 'AuthController@signUp', 'as' => 'api.auth.signUp']);
	// Route::post('change-password', ['uses' => 'PasswordController@changePassword', 'as' => 'api.password.changePassword']);
	// Route::post('auth-code', ['uses' => 'AuthController@authCode', 'as' => 'api.auth.authCode'])->middleware('auth:api');
	// Route::post('retry-code', ['uses' => 'AuthController@retryCode', 'as' => 'api.auth.retryCode'])->middleware('auth:api');
	Route::post('retry-code', ['uses' => 'AuthController@retryCode', 'as' => 'api.auth.retryCode']);
	Route::post('auth-code', ['uses' => 'AuthController@authCode', 'as' => 'api.auth.authCode']);

	Route::prefix('user')->middleware('auth:api')->group(function() {
		Route::get('profile', 'UserController@show')->name('api.user.show');
		Route::post('update', 'UserController@update')->name('api.user.update');		
		Route::post('change-password', 'PasswordController@changePassword')->name('api.password.changePassword')->middleware('auth:api');
	});

});
