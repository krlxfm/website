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
    return redirect('/home');
});
// Route::get('/', 'HomeController@welcome');

Route::get('logout', 'Auth\LoginController@logout');
Auth::routes();
Route::get('/login/password', 'Auth\LoginController@password')->name('login.password');
Route::get('/login/carleton', 'Auth\CarletonAuthController@redirect')->name('login.carleton');
Route::get('/login/callback', 'Auth\CarletonAuthController@callback');

Route::get('terms', function () {
    return view('legal.tos');
});

Route::get('privacy', function () {
    return view('legal.privacy');
});

Route::middleware(['auth', 'onboard'])->group(function () {
    Route::get('home', 'HomeController@index')->name('home');

    Route::get('board', 'BoardController@meet')->name('board.main');
    Route::get('board/meet', 'BoardController@meet')->name('board.meet');
    Route::get('board/apply', 'BoardController@index')->name('board.index');
    Route::get('board/positions', 'BoardController@positions')->name('board.positions');
    Route::get('board/apply/start', 'BoardController@start')->name('board.start');
    Route::get('board/apply/{year}', 'BoardController@myApplication')->name('board.app');
    Route::patch('board/apply/{year}', 'BoardController@updateApplication');
    Route::get('board/apply/{year}/common', 'BoardController@common')->name('board.common');
    Route::get('board/apply/{year}/logistics', 'BoardController@logistics')->name('board.logistics');

    Route::get('profile', 'UserController@profile')->name('profile');

    Route::middleware('can:create,KRLX\Show')->group(function () {
        Route::get('shows', 'ShowController@my')->name('shows.my');
        Route::get('shows/my/{term?}', 'ShowController@my')->name('shows.my.other');
    });

    Route::middleware('permission:see all applications')->group(function () {
        Route::get('shows/all/{term?}', 'ShowController@all')->name('shows.all');
        Route::get('shows/download/{term?}', 'ShowController@download')->name('shows.download');
    });

    Route::middleware('permission:see all DJs')->group(function () {
        Route::get('shows/djs/{term?}', 'ShowController@djs')->name('shows.djs');
    });

    Route::middleware('permission:build schedule')->group(function () {
        Route::get('schedule/build/{term?}', 'ScheduleController@build')->name('schedule.build');
    });

    Route::middleware(['can:create,KRLX\Show', 'contract'])->group(function () {
        Route::get('shows/join/{show?}', 'ShowController@join')->name('shows.join');
        Route::put('shows/join/{show}', 'ShowController@processJoinRequest');

        Route::get('shows/boost', 'BoostController@index')->name('boost.index');
        Route::get('shows/boost/{boost}', 'BoostController@redeem')->name('boost.redeem');
        Route::post('shows/boost/{boost}', 'BoostController@redeemToShow');
        Route::get('shows/create', 'ShowController@create')->name('shows.create');
        Route::get('shows/{show}', 'ShowController@review')->name('shows.review');
        Route::get('shows/{show}/hosts', 'ShowController@hosts')->name('shows.hosts');
        Route::get('shows/{show}/content', 'ShowController@content')->name('shows.content');
        Route::get('shows/{show}/schedule', 'ShowController@schedule')->name('shows.schedule');
        Route::get('shows/{show}/delete', 'ShowController@delete')->name('shows.delete');
        Route::post('shows', 'ShowController@store')->name('shows.store');
        Route::delete('shows/{show}', 'ShowController@destroy')->name('shows.destroy');
    });

    Route::get('contract', 'PointController@contract')->name('legal.contract');
    Route::post('contract', 'PointController@sign');
});

Route::middleware('auth')->group(function () {
    Route::get('welcome', 'HomeController@onboard')->name('legal.onboard');
    Route::post('welcome', 'HomeController@storeOnboarding');
});
