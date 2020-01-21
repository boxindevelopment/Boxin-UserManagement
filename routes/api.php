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
	Route::post('facebook/sign-up', ['uses' => 'FacebookController@signUp', 'as' => 'api.facebook.signUp']);
	Route::get('google/{token}', ['uses' => 'GoogleController@getToken', 'as' => 'api.google']);
	Route::post('password/email', 'PasswordController@forgotPassword')->name('api.password.email');
	Route::post('contact-us', 'ContactController@send')->name('api.contact');

	Route::post('sign-up', ['uses' => 'AuthController@signUp', 'as' => 'api.auth.signUp']);
	Route::post('send-code', ['uses' => 'AuthController@sendCode', 'as' => 'api.auth.sendCode']);
	Route::post('retry-code', ['uses' => 'AuthController@retryCode', 'as' => 'api.auth.retryCode']);
	Route::post('auth-code', ['uses' => 'AuthController@authCode', 'as' => 'api.auth.authCode']);
	Route::get('provinces', 'VillageController@getAllProvinces')->name('api.province.all');
	Route::get('regencies/{province_id}', 'VillageController@getAllRegencies')->name('api.regency.all');
	Route::get('districts/{regency_id}', 'VillageController@getAllDistricts')->name('api.district.all');
	Route::get('villages/{district_id}', 'VillageController@getAllVillages')->name('api.village.all');
	Route::get('help', 'HelpController@index')->name('api.help.index');
	Route::post('help', 'HelpController@store')->name('api.help.store');

	Route::prefix('user')->middleware('auth:api')->group(function() {
		Route::get('profile', 'UserController@show')->name('api.user.show');
		Route::get('address', 'UserAddressController@index')->name('api.user.address');
		Route::get('address/{id}', 'UserAddressController@show')->name('api.user.address.show');
		Route::post('address', 'UserAddressController@store')->name('api.user.address.store');
		Route::post('address/{id}/update', 'UserAddressController@update')->name('api.user.address.update');
		Route::post('set-default-address/{user_address_id}', 'UserAddressController@setDefault')->name('api.user.setDefault');
		Route::post('delete-address/{user_address_id}', 'UserAddressController@deleteAddress')->name('api.user.deleteAddress');
		Route::post('update', 'UserController@update')->name('api.user.update');
		Route::post('change-password', 'PasswordController@changePassword')->name('api.password.changePassword')->middleware('auth:api');
	});

});
