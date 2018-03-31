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


// Route::post('/register', 'UsersController@create'); //OK

Route::get('/test', 'UsersController@index' );

Route::get('/token/{token}', 'UsersController@exists'); //Le damos el token de facebook y nos devuelve el nuestro

Route::middleware('auth:api')->get('/me', 'UsersController@me');

// Route::middleware(['auth:api'])->get('/user/{user}', 'UsersController@show'); //OK

Route::middleware('auth:api')->post('/me', 'UsersController@store');

//Route::get('/users/exists/{FBid}', 'UsersController@exists'); //OK

// Route::middleware('auth:api')->get('/users', 'UsersController@index'); //OK


//Route::middleware('auth:api')->post('/perfil', 'PerfilsController@store');

Route::middleware('auth:api')->delete('/me', 'UsersController@destroy'); //OK

Route::middleware('auth:api')->get('/me/ticket', 'PurchasesController@show'); //OK

//Route::middleware('auth:api')->patch('/user/{user}', 'UsersController@index');

Route::middleware('auth:api')->get('/me/evento', 'UsersController@userevents'); //OK Hace referencia a los eventos comprados formateados para ser guardados en la database local

Route::middleware('auth:api')->get('/me/timeline/{position}/{distance}', 'UsersController@orderevents'); //Devuelve evento reordenado en función del usuario y la distancia

Route::middleware('auth:api')->get('/me/ticket/{ticket}' /* /{position}' */,'PurchasesController@ordermembers'); //Devuelve los usuarios que van a asistir a un evento del que tenemos comprado un ticket ordenados en función del rankeo de usuario, genero y edad.

// Route::middleware('auth:api')->post('/user/{user}/evento/{evento}', 'UsersController@addevento'); //OK

// Route::middleware('auth:api')->delete('/user/{user}/evento/{evento}', 'UsersController@delevento'); //OK

Route::middleware('auth:api')->get('/me/match', 'UsersController@match'); //OK

Route::middleware('auth:api')->get('/me/ticket/{ticket}/match/{user2}/{aceptado}', 'UsersController@addmatch'); //OK

Route::middleware('auth:api')->delete('/me/match/{match}', 'UsersController@delmatch'); //OK

Route::middleware('auth:api')->delete('/evento/{evento}/match', 'UsersController@delmatchonevento'); //OK
//Esto elimina todos los match de un evento en concreto pero esta ruta debería de estar capada solo a administrador

Route::middleware('auth:api')->get('/me/bloqueados', 'UsersController@bloqueados'); //Debería de devolver los bloqueados

Route::middleware('auth:api')->get('/me/bloqueadores', 'UsersController@bloqueados'); //Debería devolver los que te tienen bloqueado

Route::middleware('auth:api')->get('/me/bloqueado/{bloqueado}', 'UsersController@addbloqueado');

Route::middleware('auth:api')->delete('/me/bloqueado/{bloqueado}', 'UsersController@delbloqueado');

Route::middleware('auth:api')->get('/me/empresa', 'UsersController@showEmpresa'); 

Route::middleware('auth:api')->post('/me/empresa', 'EmpresasController@store'); //OK

Route::middleware('auth:api')->delete('/me/empresa/{empresa}', 'EmpresasController@destroy'); //OK

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

Route::middleware('auth:api')->get('/me/get/{type}/{num_tickets}/{card_token}','PurchasesController@store');

Route::get('/validate/{hash}','PurchasesController@validateTicket');

/********************************* Message interface *******************************************/

Route::middleware('auth:api')->post('/message/{id}','MessageControler@send');

/********************************* FileTransfer interface *******************************************/

Route::post('/upload/{user}','ArchiveController@store');
