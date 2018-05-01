<?php

namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function get(User $receptor)
    {
        $me = Auth::user();
	
	$emitidos = $me->messages->where('caducado',0)->where('receptor', $receptor->id);
        $recibidos = Message::all()->where('caducado',0)->where('receptor', $me->id)->where('emisor', $receptor->id);
        
        //marca el checked
        $recibidos->map(function ($item, $key) { $item['checked'] = 1; $item->save; return $item; });

        $respuesta = $emitidos->concat($recibidos);
        //Añade una columna que indica si el mensaje se debe mostrar como emisor o como receptor. Se usa en la aplicación para dar formato al mostrar la conversación
        $filtered = $respuesta->map(function($msg) use ($respuesta, $me, $receptor){
            if ($msg->emisor === $me->id && $msg->receptor === $receptor->id ) { $msg['whois'] = "emisor"; }
            if ($msg->emisor === $receptor->id && $msg->receptor === $me->id) { $msg['whois'] = "receptor"; }
	return $msg;
});

        return $filtered->sortBy(function ($msg, $key) { return $msg['created_at']; })->values();


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request, User $user)
    {

        $me = Auth::user();
        $data = $request->json()->all();

        if ( $me->bloqueadors->where('id', $user->id )->count() ){

            return;
        }

        if ( $me->matches()->where('id', $user->id ) ){

            Redis::publish('messages', json_encode(['emisor' => $me->id, 'receptor' => $user->id, 'time' => Carbon::now('Europe/Madrid')->toDateTimeString(), 'text' => $data['texto'] ]));


            

            $message = new Message();

            $message->emisor = $me->id;
            $message->receptor = $user->id;
            $message->texto = $data['texto'];

            $message->save();

            return $message->texto;

        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
