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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/test',[
    'uses' => 'TestController@getTest'
]);

Route::post('/signup',[
    'uses' => 'UserController@signup'
]);

Route::post('/signin',[
    'uses' => 'UserController@signin'
]);


Route::get('/getInfo',[
    'uses' => 'UserController@getInfo',
    'middleware' => 'auth.jwt'
]);

Route::post('/updateInfo',[
    'uses' => 'UserController@updateInfo',
    'middleware' => 'auth.jwt'
]);

Route::get('/getrate/{postid}',[
    'uses' => 'RateController@getRate'
]);

Route::get('/rate',[
    'uses' => 'RateController@rate',
    'middleware' => 'auth.jwt'
]);

Route::get('/hostels/{offset}',[
    'uses' => 'HostelController@getHostels'
]);

Route::get('/hostels/{hostelid}',[
    'uses' => 'HostelController@getHostel'
]);

Route::get('/hostels/findByPrice',[
    'uses' => 'HostelController@findByPrice'
]);

Route::get('/hostels/findByRegion',[
    'uses' => 'HostelController@findByRegion'
]);

Route::get('/hostels/findByAdd',[
    'uses' => 'HostelController@findByAdd'
]);

