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

/********************** Perfil interface  ************************************************/

//Route::post('/register','RegisterController@store');

// Route::get('/perfil', 'PerfilsController@index')

// Route::get('/perfil/{perfil}', 'PerfilsController@show');

// Route::post('/perfil', 'PerfilsController@store');

// Route::delete('/perfil/{perfil}', 'PerfilsController@destroy');

// //Route::patch('/perfil/{perfil}', 'PerfilsController@index');

// Route::get('/perfil/{perfil}/eventos', 'PerfilsController@userevents');

// Route::post('/perfil/{perfil}/eventos/{evento}', 'PerfilsController@addevento');

// Route::delete('/perfil/{perfil}/eventos/{evento}', 'PerfilsController@delevento');

// Route::get('/perfil/{perfil}/matches', 'PerfilsController@matches');

// Route::post('/perfil/{perfil}/matches/{match}/{evento}', 'PerfilsController@addmatch');

// Route::delete('/perfil/{perfil}/matches/{match}', 'PerfilsController@delmatch');

// Route::get('/perfil/{perfil}/bloqueado', 'PerfilsController@bloqueados');

// Route::post('/perfil/{perfil}/bloqueado/{bloqueado}', 'PerfilsController@addbloqueado');

// Route::delete('/perfil/{perfil}/bloqueado/{bloqueado}', 'PerfilsController@delbloqueado');

// //Route::get('/perfil/{perfil}/empresa', ''); Por el momento se supone que un usuario solo puede crear una empresa

// Route::post('/perfil/{perfil}/empresa', 'EmpresasController@store');

// Route::delete('/perfil/{perfil}/empresa/{empresa}', 'EmpresasController@destroy');

// //Route::patch('/perfil/{perfil}/empresa/{empresa}', 'PerfilsController@index');



// /****************************** Empresa interface ******************************************/

// Route::get ('/empresa','EmpresasController@index ');

// Route::get ('/empresa/{empresa}','EmpresasController@show ');

// Route::post ('/empresa/{empresa}/event','EmpresasController@addevent ');

// Route::delete ('/empresa/{empresa}/evento/{evento}','EventsController@store ');

// //Route::patch ('/empresa/{empresa}/evento/{evento}','EmpresasController@index ');

// Route::get('/empresa/{empresa}/bloqueado', 'EmpresassController@bloqueados');

// Route::post('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresassController@addbloqueado');

// Route::delete('/empresa/{empresa}/bloqueado/{bloqueado}', 'EmpresasController@delbloqueado');



// /******************************* Evento interface **********************************************/

// Route::get('/evento', 'EventsController@index');

// Route::get('/evento/{evento}', 'EventsController@show');

Auth::routes();

Route::get('/home', 'HomeController@index');
