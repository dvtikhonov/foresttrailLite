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
    return view('welcome');
});

Route::get('/bridge', 'StructuralPattrnsController@Bridge');

Route::get('/app', function () {
    return view('app');
})->name('app');

Route::get('/app-client', function () {
    return view('app_client');
})->name('app');

