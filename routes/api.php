<?php

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

Route::prefix('v1')->name('api.v1.')->namespace('API')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('shows', 'ShowController');
        Route::post('shows/remind', 'ShowController@remind');
        Route::patch('shows/{show}/hosts', 'ShowController@changeHosts');
        Route::patch('shows/{show}/invite', 'ShowController@inviteHostWithoutUserAccount');
        Route::put('shows/{show}/join', 'ShowController@join');
        Route::put('shows/{show}/submitted', 'ShowController@submit');

        Route::get('schedule/publish', 'ScheduleController@status');
        Route::patch('schedule/publish', 'ScheduleController@publish');
        Route::patch('schedule/{show}', 'ScheduleController@update');

        Route::get('users', 'UserController@search');
    });
    Route::apiResource('terms', 'TermController');
    Route::apiResource('tracks', 'TrackController');
});
