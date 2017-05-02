<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $user = new User;

        $user->name = $request->name;
        $user->surnames = $request->surnames;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->photo = $request->photo;
        $user->birthdate = $request->birthdate;
        $user->job = $request->job;
        $user->studies = $request->studies;
        $user->email = $request->email;
        $user->ranking = $request->ranking;
        $user->aceptar = $request->aceptar;
        $user->saludar = $request->saludar;
        $user->rechazar = $request->rechazar;
        $user->destacado_ini = $request->destacado_ini;
        $user->destacado_fin = $request->destacado_fin;
        //$user->location = $request->location;
    

        $user->save();

        return 1;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return $user;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::destroy($id);

        return $user;
    }


    /**
     * Listado de eventos de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userevents($id)
    {
        $user = User::find($id);
        $eventos = $user->eventos;

        return collect($eventos);
    }

    /**
     * Añadir un evento al listado de eventos de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addevento($user_id,$evento_id)
    {
        $user= User::find($user_id);
        $user->eventos()->attach($evento_id);

    }

     /**
     * Eliminar un evento del listado de eventos de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delevento($user_id,$evento_id)
    {
        $user = User::find($user_id);
        $user->eventos()->detach($evento_id);

    }

    /**
     * Mostrar los matches recíprocos de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function matches($id)
    {
        $user = User::find($id);
        $matches = $user->matches;
        $matches = $matches->creador->where('usuario2_id', $id);

        return collect ($matches);
    }

    /**
     * Crear un match un match de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addmatch($user_id, $match_id, $evento_id)
    {
        $match = new App\Match;
        $match->usuario1_id = $user_id;
        $match->usuario2_id = $match_id;
        $match->evento_id = $evento_id;
        $match->save();

        return $match;

    }

    /**
     * Eliminar un match un match de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delmatch($user_id, $match_id)
    {
        $user = User::find($user_id);
        $match = $user->matches()->where('usuario2_id', $match_id);
        $match->delete();
    
        return $match;

    }

    /**
     * Devolver el listado de bloqueados de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados($user_id)
    {
        
        $user = User::find($user_id);
        $bloqueados = $user->bloqueados;

        return collect($bloqueados);

    }

    /**
     * Añadir un bloqueado a un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado($user_id, $bloqueado_id)
    {
        
        $bloqueado = new App\Bloqueado;
        $bloqueado->bloqueado = $bloqueado_id;
        $bloqueado->bloqueador_type = 'Illuminate\Foundation\Auth\User';
        $bloqueado->bloqueador_id = $user_id;
        $bloqueado->save();


        return $bloqueado;

    }

    /**
     * Eliminar un bloqueado de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado($user_id, $bloqueado_id)
    {
        $user = User::find($user_id);
        $bloqueados = $user->bloqueados;
        $bloqueado = $bloqueados->where('bloqueado_id', $bloqueado_id);
        $bloqueado->delete();

        return $bloqueado;

    }

    /**
     * Eliminar una empresa de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delempresa($user_id, $empresa_id)
    {
        $user = User::find($user_id);
        $bloqueados = $user->bloqueados;
        $bloqueado = $bloqueados->where('bloqueado_id', $bloqueado_id);
        $bloqueado->delete();

        return $bloqueado;

    }
}
