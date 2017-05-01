<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Perfil;

class PerfilsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $perfils = Perfil::all();

        return $perfils;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $perfil = new Perfil;
        
        $perfil->last_connection = 'NOW()';
        $perfil->name = $request->name;
        $perfil->surnames = $request->surnames;
        $perfil->photo = $request->photo;
        $perfil->birthdate = $request->birthdate;
        $perfil->job = $request->job;
        $perfil->studies = $request->studies;
        $perfil->email = $request->email;
        $perfil->ranking = $request->ranking;
        $perfil->aceptar = $request->aceptar;
        $perfil->saludar = $request->saludar;
        $perfil->rechazar = $request->rechazar;
        $perfil->destacado_ini = $request->destacado_ini;
        $perfil->destacado_fin = $request->destacado_fin;
        //$perfil->location = $request->location;
    

        $perfil->save();

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
        $perfil = Perfil::find($id);

        return $perfil;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $perfil=Perfil::destroy($id);

        return $perfil;
    }

    /**
     * Listado de eventos de un perfil concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userevents($id)
    {
        $perfil = Perfil::find($id);
        $eventos = $perfil->eventos;

        return collect($eventos);
    }

    /**
     * Añadir un evento al listado de eventos de un perfil concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addevento($perfil_id,$evento_id)
    {
        $perfil = Perfil::find($perfil_id);
        $perfil->eventos()->attach($evento_id);

    }

     /**
     * Eliminar un evento del listado de eventos de un perfil concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delevento($perfil_id,$evento_id)
    {
        $perfil = Perfil::find($perfil_id);
        $perfil->eventos()->detach($evento_id);

    }

    /**
     * Mostrar los matches recíprocos de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function matches($id)
    {
        $perfil = Perfil::find($id);
        $matches = $perfil->matches;
        $matches = $matches->creador->where('usuario2_id', $id);

        return collect ($matches);
    }

    /**
     * Crear un match un match de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addmatch($perfil_id, $match_id, $evento_id)
    {
        $match = new App\Match;
        $match->usuario1_id = $perfil_id;
        $match->usuario2_id = $match_id;
        $match->evento_id = $evento_id;
        $match->save();

        return $match;

    }

    /**
     * Eliminar un match un match de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delmatch($perfil_id, $match_id)
    {
        $perfil = Perfil::find($perfil_id);
        $match = $perfil->matches()->where('usuario2_id', $match_id);
        $match->delete();
    
        return $match;

    }

    /**
     * Devolver el listado de bloqueados de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados($perfil_id)
    {
        
        $perfil = Perfil::find($perfil_id);
        $bloqueados = $perfil->bloqueados;

        return collect($bloqueados);

    }

    /**
     * Añadir un bloqueado a un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado($perfil_id, $bloqueado_id)
    {
        
        $bloqueado = new App\Bloqueado;
        $bloqueado->bloqueado = $bloqueado_id;
        $bloqueado->bloqueador_type = 'App\Perfil';
        $bloqueado->bloqueador_id = $perfil_id;
        $bloqueado->save();


        return $bloqueado;

    }

    /**
     * Eliminar un bloqueado de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado($perfil_id, $bloqueado_id)
    {
        $perfil = Perfil::find($perfil_id);
        $bloqueados = $perfil->bloqueados;
        $bloqueado = $bloqueados->where('bloqueado_id', $bloqueado_id);
        $bloqueado->delete();

        return $bloqueado;

    }

    /**
     * Eliminar una empresa de un perfil
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delempresa($perfil_id, $empresa_id)
    {
        $perfil = Perfil::find($perfil_id);
        $bloqueados = $perfil->bloqueados;
        $bloqueado = $bloqueados->where('bloqueado_id', $bloqueado_id);
        $bloqueado->delete();

        return $bloqueado;

    }
}
