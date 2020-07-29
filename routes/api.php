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
    Route::get('/user', 'UserController@getById');
    Route::post('/ride', 'RidesController@store');
    Route::get('/myRides', 'RidesController@index');
    Route::post('/request', 'RequestsController@store');
    Route::get('/myRequests', 'RequestsController@index');
    Route::get('/availableRides', 'RequestsController@viewAvailableRides');
    Route::post('/editUser', 'UserController@edit');
    Route::post('/deleteRide', 'RidesController@destroy');
    Route::post('/deleteRequest', 'RequestsController@destroy');
    Route::post('/deleteUser', 'UserController@destroy');
    Route::put('/request', 'RidesController@acceptRequest');
    Route::post('/updateRide', 'RidesController@update');
    Route::post('/updateRequest', 'RequestsController@update');


});
