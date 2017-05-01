<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Evento;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventos = Evento::all();

        return $evento;
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
    public function store(Request $request, Empresa $empresa)
    {
        $evento = new Evento;

        $evento->creator = $empresa;
        $evento->nombre = $request->nombre ;
        $evento->photo = $request->photo ;
        $evento->event_ini = $request->event_ini ;
        $evento->event_fin = $request->event_fin ;
        $evento->price = $request->price ;
        $evento->aforo = $request->aforo ;
        $evento->destacado_ini = $request->destacado_ini ;
        $evento->destacado_fin = $request->destacado_fin ;
        $evento->location = $request->location ;

        $evento->save();

        return $evento;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $evento = Evento::find($id);

        return $evento;
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
        $evento = Evento::destroy($id);

        return $evento;
    }

    


}