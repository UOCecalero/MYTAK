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
        
        //marca el checked antes de entregar los mensajes
        $recibidos->map(function ($item, $key) { $item['checked'] = 1; $item->save; return $item; });

        $respuesta = $emitidos->concat($recibidos);
        
        //Añade una columna que indica si el mensaje se debe mostrar como emisor o como receptor. Se usa en la aplicación para dar formato al mostrar la conversación
        $filtered = $respuesta->map(function($msg) use ($respuesta, $me, $receptor){

            if ($msg->emisor === $me->id && $msg->receptor === $receptor->id ) { $msg['whois'] = "emisor"; }
            if ($msg->emisor === $receptor->id && $msg->receptor === $me->id) { $msg['whois'] = "receptor"; }
            $time = $msg->created_at;
            $msg["time"] = $time->toTimeString();
            return $msg;
        });

        return $filtered->sortBy(function ($msg, $key) { return $msg['created_at']; })->values();

        //Formato de los mensajes

        // "id": 1,
        // "created_at": "2018-05-01 13:43:58",
        // "updated_at": "2018-05-01 13:43:58",
        // "conversation": 0, --> este campo ha sido eliminado
        // "checked": 0,
        // "emisor": 86,
        // "receptor": 90,
        // "receptor_token": "jkdsnlfkjlksjnf8747358b88f8ni894b9fub98brid",
        // "texto": "Hola! Que tal? ;P",
        // "caducado": 0,
        // "whois": "emisor",
        // "time": "13:43:58"

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


            // Redis::publish('messages', json_encode([
            //     'emisor' => $me->id, 
            //     //'receptor' => $user->id,
            //     /**
            //         Lo manda al token. Cuando el usuario se conecta se identifica con el token para recibir los mensajes. Por lo tanto la unica forma de suplantar al usuario es tener el token. En caso de que alguien lo consiguiese, caduca.
            //     **/
            //     'receptor' => $user->tokens[0],
            //     'time' => Carbon::now('Europe/Madrid')->toDateTimeString(), 
            //     'text' => $data['texto']
            // ]) );



            $message = new Message();

            $message->emisor = $me->id;
            $message->receptor = $user->id;

            if ( $isset($user->tokens[0] )) { 

                $message->receptor_token = $user->tokens[0];

            } else {

                $message->receptor_token = null;
            }
            

            $message->texto = $data['texto'];

            $message->save();


            Redis::publish('messages', json_encode([ $message ]) );

            return $message;

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
