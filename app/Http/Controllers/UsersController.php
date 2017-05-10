<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Evento;
use App\Match;
use App\Empresa;
use App\Bloqueado;
use App\User;


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

        $user->last_connection ='NOW()';
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

        return $user;
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
    public function userevents(User $user)
    {
        $eventos = $user->eventos;

        return $eventos;
    }

    /**
     * Añadir un evento al listado de eventos de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addevento(User $user, Evento $evento)
    {
        $user->eventos()->attach($evento);

        return 1;
    }

     /**
     * Eliminar un evento del listado de eventos de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delevento(User $user, Evento $evento)
    {
        $result = $user->eventos->where('id',$evento->id);

        if( $result->isEmpty() != 'true')
        {
        
        $user->eventos()->detach($evento);
        return 1;
        
        } else return 'Este usuario no tiene el evento: '. $evento ;

    }

    /**
     * Mostrar los matches recíprocos de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function match(User $user)
    {   
        
        $matches = $user->matches();

        if ($matches->isEmpty()){ return 'Este usuario aun no tiene ningun match'; }
        else{ return $matches; }
    }

    /**
     * Crear un match un match de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addmatch(User $user, User $user2, Evento $evento)
    {   
        $res1 = $user->eventos->where('id', $evento->id);
        $res2 = $user2->eventos->where('id', $evento->id);

        if( $res1->isEmpty() == 'true' OR $res2->isEmpty() == 'true'  )
        { 
            return 'Algun o ambos usuarios no estan en el evento correcto';
        }

        else { 
                $res = Match::where('usuario1_id', $user->id)
                ->where('usuario2_id', $user2->id)
                ->get();

        if (  $res->isEmpty() )

            {
            $match = new Match;
            $match->usuario1_id = $user->id;
            $match->usuario2_id = $user2->id;
            $match->evento_id = $evento->id;
            $match->save();
            }

        return 1;
        }
    }

    /**
     * Eliminar un match un match de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delmatch(User $user, User $match)
    {
        $res = Match::where('usuario1_id', $user->id)->where('usuario2_id', $match->id)->get();

        if ($res->isEmpty())
        {
        
        return 'El usuario '.$user->name.' '.$user->surnames.' no tiene ningun match con '.$match->name.' '.$match->surnames;
        
        } 
        
        else
        
        {
             $id = $res[0]->id;
             Match::destroy($id);
            return $match;
        }
        
        
    

    }

    /**
     * Devolver el listado de bloqueados de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados(User $user)
    {
        $bloqueados = $user->bloqueados;

        if($bloqueados->isNotEmpty())
        {
        return $bloqueados;
        }
        else return 'No hay usuarios bloqueados para este usuario';
    }  

    /**
     * Añadir un bloqueo a un user dede user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado(User $user, User $bloqueado)
    {   

        $user->bloqueados()->attach($bloqueado->id);

    }

    /**
     * Eliminar un bloqueado de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado(User $user, User $bloqueado)
    {
        
        $user->bloqueados()->detach($bloqueado->id);

        return 1

    }

    /**
     * Eliminar una empresa de un user 
     (Mientras un usuario solo temga una empresa con borrar la empresa con empresascontroller@destroy es suficiente)
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    public function delempresa(User $user, Empresa $empresa)
    {
        
        
        if ( $user->empresa == $empresa ){

        $empresa = $user->empresa->where('id', $empresa->id);
        $empresa->delete();

        return $empresa;
        }

        else return 'La empresa que quieres borrar no corresponde a este usuario';

    } 
} **/
