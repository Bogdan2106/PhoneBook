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

Route::get('/', 'ContactsController@index');
Route::post('/contacts/store', 'ContactsController@store');
Route::get('/contacts/delete/{id}', 'ContactsController@delete');
Route::put('/contacts/update/{id}', 'ContactsController@update');
