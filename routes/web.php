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
    return view('index');
});
Route::get('/asociados', 'AsociadoController@index');
Route::post('/asociados/save', 'AsociadoController@store');
Route::put('/asociados/update', 'AsociadoController@update');
Route::put('/asociados/desactivar', 'AsociadoController@desactivar');
Route::put('/asociados/activar', 'AsociadoController@activar');
