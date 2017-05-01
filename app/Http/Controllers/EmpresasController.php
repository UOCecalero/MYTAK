<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    public function store(Request $request, Perfil $perfil)
    {
        $empresa = new Empresa;

        $empresa->last_connection = 'NOW()';
        $empresa->name = $request->name ;
        $empresa->creator = $perfil->id;
        $empresa->email = $request->email ;
        $empresa->pwd = $request->pwd ;
        $empresa->web = $request->web ;

        $empresa->save();
        
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
    public function destroy($perfil_id, $empresa_id)
    {   
        $perfil = App\Perfil::find($perfil_id);
        $empresa = $perfil->empresa->where('id', $empresa_id);
        $empresa->delete();

        return $empresa;
    }

    /**
     * Devolver el listado de bloqueados de una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados($empresa_id)
    {
        
        $empresa = Empresa::find($empresa_id);
        $bloqueados = $empresa->bloqueados;

        return collect($bloqueados);

    }

    /**
     * Añadir un bloqueado a una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado($empresa_id, $bloqueado_id)
    {
        
        $bloqueado = new App\Bloqueado;
        $bloqueado->bloqueado = $bloqueado_id;
        $bloqueado->bloqueador_type = 'App\Empresa';
        $bloqueado->bloqueador_id = $empresa_id;
        $bloqueado->save();


        return $bloqueado;

    }

    /**
     * Eliminar un bloqueado de una empresa
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado($empresa_id, $bloqueado_id)
    {
        $empresa = Perfil::find($empresa_id);
        $bloqueados = $empresa->bloqueados;
        $bloqueado = $bloqueados->where('bloqueado_id', $bloqueado_id);
        $bloqueado->delete();

        return $bloqueado;

    }
}

