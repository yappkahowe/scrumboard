<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/sign-in', 'Auth\LoginController@redirectToGoogle');
Route::get('/sign-in/google', 'Auth\LoginController@signinWithGoogle');
Route::get('/sign-out', 'Auth\LoginController@logout');

Route::get('/{vue_capture?}', function () {
    return view('app');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');