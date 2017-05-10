<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;

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

Route::post('/register', 'UsersController@store'); //OK

Route::middleware('auth:api')->get('/users', 'UsersController@index'); //OK

Route::middleware('auth:api')->get('/user/{user}', 'UsersController@show'); //OK

//Route::middleware('auth:api')->post('/perfil', 'PerfilsController@store');

Route::middleware('auth:api')->delete('/user/{user}', 'UsersController@destroy'); //OK

//Route::middleware('auth:api')->patch('/user/{user}', 'UsersController@index');

Route::middleware('auth:api')->get('/user/{user}/evento', 'UsersController@userevents'); //OK

Route::middleware('auth:api')->post('/user/{user}/evento/{evento}', 'UsersController@addevento'); //OK

Route::middleware('auth:api')->delete('/user/{user}/evento/{evento}', 'UsersController@delevento'); //OK

Route::middleware('auth:api')->get('/user/{user}/match', 'UsersController@match'); //OK

Route::middleware('auth:api')->post('/user/{user}/match/{user2}/{evento}', 'UsersController@addmatch'); //OK

Route::middleware('auth:api')->delete('/user/{user}/match/{match}', 'UsersController@delmatch'); //OK

Route::middleware('auth:api')->get('/user/{user}/bloqueado', 'UsersController@bloqueados'); 

Route::middleware('auth:api')->post('/user/{user}/bloqueado/{bloqueado}', 'UsersController@addbloqueado');

Route::middleware('auth:api')->delete('/user/{user}/bloqueado/{bloqueado}', 'UsersController@delbloqueado');

//Route::middleware('auth:api')->get('/user/{user}/empresa', ''); Por el momento se supone que un usuario solo puede crear una empresa

Route::middleware('auth:api')->post('/user/{user}/empresa', 'EmpresasController@store'); //OK

Route::middleware('auth:api')->delete('/user/{user}/empresa/{empresa}', 'EmpresasController@destroy'); //OK

//Route::middleware('auth:api')->patch('/user/{user}/empresa/{empresa}', 'UsersController@index');



/****************************** Empresa interface ******************************************/

Route::middleware('auth:api')->get ('/empresa','EmpresasController@index'); //OK

Route::middleware('auth:api')->get ('/empresa/{empresa}','EmpresasController@show'); //OK

Route::middleware('auth:api')->get ('/empresa/{empresa}/eventos','EmpresasController@showeventos'); //OK

Route::middleware('auth:api')->post ('/empresa/{empresa}/evento','EventosController@store'); //OK

Route::middleware('auth:api')->delete ('/empresa/{empresa}/evento/{evento}','EventosController@destroy');

//Route::middleware('auth:api')->patch ('/empresa/{empresa}/evento/{evento}','EmpresasController@index ');

Route::middleware('auth:api')->get('/empresa/{empresa}/bloqueado', 'EmpresasController@bloqueados');

Route::middleware('auth:api')->post('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@addbloqueado'); //OK

Route::middleware('auth:api')->delete('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@delbloqueado');



/******************************* Evento interface **********************************************/

Route::middleware('auth:api')->get('/eventos', 'EventosController@index'); //OK

Route::middleware('auth:api')->get('/evento/{evento}', 'EventosController@show'); //OK
