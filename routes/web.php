<?php

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

Auth::routes(['verify' => true]); 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/validate-token', function () {
    return ['data' => 'Token is valid'];
})->middleware('auth:api');

Route::get('/home', 'HomeController@index');
