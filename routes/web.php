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

Route::middleware(['auth', 'onboard'])->group(function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('shows', 'ShowController@my')->name('shows.my');
    Route::get('shows/my/{term?}', 'ShowController@my')->name('shows.my.other');
    Route::get('shows/all/{term?}', 'ShowController@all')->name('shows.all');
    Route::get('shows/djs/{term?}', 'ShowController@djs')->name('shows.djs');

    Route::middleware('contract')->group(function () {
        Route::get('shows/join/{show?}', 'ShowController@join')->name('shows.join');
        Route::put('shows/join/{show}', 'ShowController@processJoinRequest');

        Route::get('shows/create', 'ShowController@create')->name('shows.create');
        Route::get('shows/{show}', 'ShowController@review')->name('shows.review');
        Route::get('shows/{show}/hosts', 'ShowController@hosts')->name('shows.hosts');
        Route::get('shows/{show}/content', 'ShowController@content')->name('shows.content');
        Route::get('shows/{show}/schedule', 'ShowController@schedule')->name('shows.schedule');
        Route::post('shows', 'ShowController@store')->name('shows.store');
    });

    Route::get('schedule/build/{term?}', 'ScheduleController@build')->name('schedule.build');

    Route::get('contract', 'PointController@contract')->name('legal.contract');
    Route::post('contract', 'PointController@sign');
});

Route::middleware('auth')->group(function () {
    Route::get('welcome', 'HomeController@onboard')->name('legal.onboard');
    Route::post('welcome', 'HomeController@storeOnboarding');
});
