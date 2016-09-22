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

Route::get('/me', 'UserController@me');
Route::post('/me', 'UserController@updateMyself');
Route::get('/users', 'UserController@index');
Route::get('/teammates', 'UserController@teammates');
Route::post('/users', 'UserController@store');
Route::delete('/users/{user}', 'UserController@delete');
Route::post('/users/{id}', 'UserController@restore');

Route::get('/stages', 'StageController@index');

Route::get('/my-tasks', 'TaskController@myTasks');
Route::post('/tasks', 'TaskController@store');
Route::patch('/tasks/{task}', 'TaskController@update');
Route::delete('/tasks/{task}', 'TaskController@delete');
Route::post('/tasks/{id}', 'TaskController@restore');