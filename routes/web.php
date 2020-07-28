<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/showCarForm', 'ProfilesController@showCarForm')->name('car.showCarForm');
    Route::post('/FillcarDetails/{user_id}', 'ProfilesController@fillDetails')->name('car.fillDetails');

    Route::resource('/requestts', 'RequesttsController');
    Route::resource('/rides', 'RidesController');
    Route::get('/requestts/{id}/AvailableRides', 'RequesttsController@viewAvailableRides')->name('requestts.viewAvailableRides');
    Route::get('/requestts/{request_id}/AvailableRides/{ride_id}', 'RequesttsController@sendRequest')->name('requsetts.sendRequest');
    Route::get('/requestts/{request_id}/myRide/{ride_id}', 'RequesttsController@cancelRide')->name('requestts.cancelRide');

    //
    Route::get('/rides/{id}/AvailableRequests', 'RidesController@viewSentRequests')->name('rides.viewSentRequests');
    Route::get('/rides/{request_id}/AvailableRequests/{ride_id}', 'RidesController@acceptRequest')->name('rides.acceptRequest');

    //
    Route::get('/profiles/{user_id}/profile', 'ProfilesController@showProfile')->name('users.showProfile');
    Route::get('/profiles/{user_id}/profile/edit', 'ProfilesController@edit')->name('profile.edit');
    Route::post('/profiles/{user_id}/profile/edit', 'ProfilesController@update')->name('profile.update');
});
