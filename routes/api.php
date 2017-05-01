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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/perfil', 'PerfilsController@index');

Route::middleware('auth:api')->get('/perfil/{perfil}', 'PerfilsController@show');

Route::middleware('auth:api')->post('/perfil', 'PerfilsController@store');

Route::middleware('auth:api')->delete('/perfil/{perfil}', 'PerfilsController@destroy');

//Route::middleware('auth:api')->patch('/perfil/{perfil}', 'PerfilsController@index');

Route::middleware('auth:api')->get('/perfil/{perfil}/eventos', 'PerfilsController@userevents');

Route::middleware('auth:api')->post('/perfil/{perfil}/eventos/{evento}', 'PerfilsController@addevento');

Route::middleware('auth:api')->delete('/perfil/{perfil}/eventos/{evento}', 'PerfilsController@delevento');

Route::middleware('auth:api')->get('/perfil/{perfil}/matches', 'PerfilsController@matches');

Route::middleware('auth:api')->post('/perfil/{perfil}/matches/{match}/{evento}', 'PerfilsController@addmatch');

Route::middleware('auth:api')->delete('/perfil/{perfil}/matches/{match}', 'PerfilsController@delmatch');

Route::middleware('auth:api')->get('/perfil/{perfil}/bloqueado', 'PerfilsController@bloqueados');

Route::middleware('auth:api')->post('/perfil/{perfil}/bloqueado/{bloqueado}', 'PerfilsController@addbloqueado');

Route::middleware('auth:api')->delete('/perfil/{perfil}/bloqueado/{bloqueado}', 'PerfilsController@delbloqueado');

//Route::middleware('auth:api')->get('/perfil/{perfil}/empresa', ''); Por el momento se supone que un usuario solo puede crear una empresa

Route::middleware('auth:api')->post('/perfil/{perfil}/empresa', 'EmpresasController@store');

Route::middleware('auth:api')->delete('/perfil/{perfil}/empresa/{empresa}', 'EmpresasController@destroy');

//Route::middleware('auth:api')->patch('/perfil/{perfil}/empresa/{empresa}', 'PerfilsController@index');



/****************************** Empresa interface ******************************************/

Route::middleware('auth:api')->get ('/empresa','EmpresasController@index ');

Route::middleware('auth:api')->get ('/empresa/{empresa}','EmpresasController@show ');

Route::middleware('auth:api')->post ('/empresa/{empresa}/event','EmpresasController@addevent ');

Route::middleware('auth:api')->delete ('/empresa/{empresa}/evento/{evento}','EventsController@store ');

//Route::middleware('auth:api')->patch ('/empresa/{empresa}/evento/{evento}','EmpresasController@index ');

Route::middleware('auth:api')->get('/empresa/{empresa}/bloqueado', 'EmpresassController@bloqueados');

Route::middleware('auth:api')->post('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresassController@addbloqueado');

Route::middleware('auth:api')->delete('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@delbloqueado');



/******************************* Evento interface **********************************************/

Route::middleware('auth:api')->get('/evento', 'EventsController@index');

Route::middleware('auth:api')->get('/evento/{evento}', 'EventsController@show');
