<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('reserva/show/{id1}/{id2}', 'ReservaController@show')->name('show');
Route::get('reserva/busqueda', 'ReservaController@buscarReserva');
Route::post('reserva/resultado', 'ReservaController@resultado');
Route::resource('reserva', 'ReservaController');
Route::get('reserva/show/{id}', 'ReservaController@show');
Route::get('reserva/imprimir/{id1}/{id2}', 'ReservaController@imprimir');
Route::get('viaje/buscar', 'ViajeController@buscar');
Route::post('viaje/resultado', 'ViajeController@resultado');
//Route::get('reserva/show/{id}',array('as'=>'htmltopdfview','uses'=>'ReservaController@show'));

// Rutas de login y autenticación
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

//Reg routes
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');


Route::get('/logout', function () {
    return view('logout');
});