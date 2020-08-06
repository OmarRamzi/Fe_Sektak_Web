<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'api'], function () {
    Route::get('/login', 'UserController@login');
});

Route::group(['namespace' => 'api'], function () {
    Route::post('/register', 'UserController@register');
    Route::get('/notifications', 'NotificationController@showNotifications');
    Route::get('/getUnReadNotificationsCount', 'NotificationController@getUnReadNotificationsCount');
    Route::post('/review', 'UserController@calcUserTotalReview');
    Route::get('/user', 'UserController@getById');
    Route::put('/user', 'UserController@edit');
    Route::delete('/user', 'UserController@destroy');
    Route::post('updateProfilePicture', 'UserController@updateProfilePicture');


    Route::get('/allRequests', 'RequestsController@index');
    Route::post('/request', 'RequestsController@store');
    Route::put('/sendRequest', 'RequestsController@sendRequest');
    Route::put('/request', 'RequestsController@update');
    Route::delete('/request', 'RequestsController@destroy');
    Route::put('/rejectRequest', 'RequestsController@reject');

    Route::get('/allRides', 'RidesController@index');
    Route::get('/availableRides', 'RidesController@viewAvailableRides');
    Route::put('/acceptRequest', 'RequestsController@acceptRequest');
    Route::put('/cancelRequest', 'RequestsController@cancelRequest');
    Route::post('/ride', 'RidesController@store');
    Route::delete('/ride', 'RidesController@destroy');
    Route::put('/ride', 'RidesController@update');

    Route::post('/userLocation', 'UserController@send');
});
