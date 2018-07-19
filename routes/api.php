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

	Route::prefix('user')->middleware('auth:api')->group(function() {
		Route::get('profile', 'UserController@show')->name('api.user.show');
		Route::post('update', 'UserController@update')->name('api.user.update');
	});
});
