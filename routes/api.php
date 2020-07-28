<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace' => 'api'], function () {
    Route::get('/login', 'UserController@login');
});

Route::group(['namespace' => 'api'], function () {
    Route::post('/register', 'UserController@register');
    Route::get('/myRides', 'RidesController@index');
    Route::post('/ride', 'RidesController@store');
    Route::post('/request', 'RequesttsController@store');
    Route::get('/myRequests', 'RequesttsController@index');

    Route::get('/availableRides', 'RequesttsController@viewAvailableRides');




});
