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

Route::get('landing', function () {
    return view('welcome');
});

Auth::routes();
Route::get('/login/password', 'Auth\LoginController@password')->name('login.password');
Route::get('/login/carleton', 'Auth\CarletonAuthController@redirect')->name('login.carleton');
Route::get('/login/callback', 'Auth\CarletonAuthController@callback');
Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function() {
    Route::get('shows', 'ShowController@my')->name('shows.my');
});
