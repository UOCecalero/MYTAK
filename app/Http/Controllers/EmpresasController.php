<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Empresa;

class EmpresasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $empresas = Empresa::all();

        return $empresas;
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
        $user = Auth::user();

        $empresa = new Empresa;

        $empresa->name = $request->name;
        $empresa->creator = $user->id;
        $empresa->email = $request->email;
        $empresa->pwd = Hash::make($request->pwd);
        $empresa->web = $request->web;

        $empresa->save();

        return $empresa;
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $empresa = Empresa::find($id);

        return $empresa;
    }


    /**
     * Muestra los eventos propiedad de una empresa 
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showeventos(Empresa $empresa)
    {
        $eventos = $empresa->eventos;

        return $eventos;
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
    public function destroy( Empresa $empresa)
    {   
        $user = Auth::user();
           if ($user->empresa == $empresa )
            {

                $empresa->delete();
                return 1;

            } else return 0;

    }

    /**
     * Devolver el listado de bloqueados de una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados(Empresa $empresa)
    {
        
        $bloqueados = $empresa->bloqueados;

        if($bloqueados->isNotEmpty())
        {
        return $bloqueados;
        }
        else return 'No hay usuarios bloqueados para esta empresa';
    }

    /**
     * Añadir un bloqueado a una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado(Empresa $empresa, User $bloqueado)
    {
        
        
        $empresa->bloqueados()->attach($bloqueado->id);


        return $bloqueado;

    }

    /**
     * Eliminar un bloqueado de una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado(Empresa $empresa, User $bloqueado)
    {   
        $res = $empresa->bloqueados->where('id', $bloqueado->id);

        //Si existe como bloqueado lo quita de la lista y sino no hace nada y devuelve 1 igualmente.
        
        if ( $res->isNotEmpty()  ){
        $empresa->bloqueados()->detach($bloqueado->id);
        }
        return 1;

    }
}

