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
| is assigned the "api" middleware group. Enjoy building your API!user
|
*/

Route::post('/register', 'UsersController@create'); //OK

Route::middleware('auth:api')->get('/user/{user}', 'UsersController@show'); //OK

Route::middleware('auth:api')->post('/user/{user}', 'UsersController@store');

Route::get('/users/exists/{FBid}', 'UsersController@exists'); //OK

Route::middleware('auth:api')->get('/users', 'UsersController@index'); //OK


//Route::middleware('auth:api')->post('/perfil', 'PerfilsController@store');

Route::middleware('auth:api')->delete('/user/{user}', 'UsersController@destroy'); //OK

Route::middleware('auth:api')->get('/user/{user}/ticket', 'PurchasesController@show');

//Route::middleware('auth:api')->patch('/user/{user}', 'UsersController@index');

Route::middleware('auth:api')->get('/user/{user}/evento', 'UsersController@userevents'); //OK Hace referencia a los eventos comprados formateados para ser guardados en la database local

Route::middleware('auth:api')->get('/user/{user}/timeline/{position}/{distance}', 'UsersController@orderevents'); //Devuelve evento reordenado en función del usuario y la distancia

Route::middleware('auth:api')->get('/user/{user}/ticket/{ticket}' /* /{position}' */,'PurchasesController@ordermembers'); //Devuelve los usuarios que van a asistir a un evento del que tenemos comprado un ticket ordenados en función del rankeo de usuario, genero y edad.

// Route::middleware('auth:api')->post('/user/{user}/evento/{evento}', 'UsersController@addevento'); //OK

// Route::middleware('auth:api')->delete('/user/{user}/evento/{evento}', 'UsersController@delevento'); //OK

Route::middleware('auth:api')->get('/user/{user}/match', 'UsersController@match'); //OK

Route::middleware('auth:api')->get('/user/{user}/ticket/{ticket}/match/{user2}/{es_aceptado}', 'UsersController@addmatch'); //OK

Route::middleware('auth:api')->delete('/user/{user}/match/{match}', 'UsersController@delmatch'); //OK

Route::middleware('auth:api')->delete('/evento/{evento}/match', 'UsersController@delmatchonevento'); //OK

Route::middleware('auth:api')->get('/user/{user}/bloqueados', 'UsersController@bloqueados'); //Debería de devolver los bloqueados

Route::middleware('auth:api')->get('/user/{user}/bloqueadores', 'UsersController@bloqueados'); //Debería devolver los que te tienen bloqueado

Route::middleware('auth:api')->post('/user/{user}/bloqueado/{bloqueado}', 'UsersController@addbloqueado');

Route::middleware('auth:api')->delete('/user/{user}/bloqueado/{bloqueado}', 'UsersController@delbloqueado');

Route::middleware('auth:api')->get('/user/{user}/empresa', 'UsersController@showEmpresa'); 

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

Route::middleware('auth:api')->get('/empresa/{empresa}/bloqueado', 'EmpresasController@bloqueados'); //OK

Route::middleware('auth:api')->post('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@addbloqueado');//OK

Route::middleware('auth:api')->delete('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@delbloqueado');
//OK


/******************************* Evento interface **********************************************/

//La devolución de los eventos solo se hace en base a ciertos parámetros
//Route::middleware('auth:api')->get('/eventos', 'EventosController@index'); //OK
//Route::middleware('auth:api')->post('/eventos', 'EventosController@index'); //Futuro método pasar parametros


Route::middleware('auth:api')->get('/evento/{evento}', 'EventosController@show'); //OK


/********************************* Ticket interface *******************************************/

// Route::middleware('auth:api')->post('/customer/{user}','PurchasesController@newCustomer');

Route::middleware('auth:api')->get('/customer/{user}/{type}/{num_tickets}/{card_token}','PurchasesController@store');

Route::get('/validate/{hash}','PurchasesController@validateTicket');

/********************************* FileTransfer interface *******************************************/

Route::post('/upload/{user}','ArchiveController@store');
