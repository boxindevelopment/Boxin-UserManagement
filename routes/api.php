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
	Route::post('register', ['uses' => 'AuthController@register', 'as' => 'api.auth.getToken']);
	Route::post('login', ['uses' => 'AuthController@login', 'as' => 'api.auth.getToken']);
	Route::get('facebook/{token}', ['uses' => 'FacebookController@getToken', 'as' => 'api.facebook']);
	Route::get('google/{token}', ['uses' => 'GoogleController@getToken', 'as' => 'api.google']);
	Route::post('password/email', 'PasswordController@sendEmailReset')->name('api.password.email');
	Route::post('password/reset', 'PasswordController@changePassword')->name('api.password.reset');

	Route::post('sign-up', ['uses' => 'AuthController@signUp', 'as' => 'api.auth.signUp']);
	Route::post('set-password', ['uses' => 'PasswordController@setPassword', 'as' => 'api.password.setPassword']);
	Route::post('auth-code', ['uses' => 'AuthController@authCode', 'as' => 'api.auth.authCode']);
	Route::post('retry-code/{user_id}', ['uses' => 'AuthController@retryCode', 'as' => 'api.auth.retryCode']);

	Route::prefix('user')->middleware('auth:api')->group(function() {
		Route::get('profile', 'UserController@show')->name('api.user.show');
		Route::post('update', 'UserController@update')->name('api.user.update');
	});
});
