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

Route::get('/', function () {
    return view('root');
});

Route::get('landing', function () {
    return view('welcome');
});

Route::get('logout', 'Auth\LoginController@logout');
Auth::routes();
Route::get('/login/password', 'Auth\LoginController@password')->name('login.password');
Route::get('/login/carleton', 'Auth\CarletonAuthController@redirect')->name('login.carleton');
Route::get('/login/callback', 'Auth\CarletonAuthController@callback');
Route::get('/home', 'HomeController@index')->name('home');

Route::middleware('auth')->group(function () {
    Route::get('shows', 'ShowController@my')->name('shows.my');
    Route::get('shows/all', 'ShowController@all')->name('shows.all');
    Route::get('shows/create', 'ShowController@create')->name('shows.create');
    Route::get('shows/{show}', 'ShowController@review')->name('shows.review');
    Route::get('shows/{show}/hosts', 'ShowController@hosts')->name('shows.hosts');
    Route::get('shows/{show}/content', 'ShowController@content')->name('shows.content');
    Route::get('shows/{show}/schedule', 'ShowController@schedule')->name('shows.schedule');
    Route::post('shows', 'ShowController@store')->name('shows.store');
});
