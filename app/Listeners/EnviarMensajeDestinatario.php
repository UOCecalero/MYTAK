<?php

namespace App\Listeners;

use App\Events\MensajeRecibido;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class EnviarMensajeDestinatario
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  MensajeRecibido  $event
     * @return void
     */
    public function handle(MensajeRecibido $event)
    {
        $token = \Laravel\Passport\Token::findOrFail($event->token);
        $current_date = \Carbon\Carbon::now();
        if ( !$token->revoked && ($token->expires_at > $current_date) ){

            $emisor = \App\User::findOrFail($token->user_id);

            
            $receptor = \App\User::findOrFail($event->message->receptor);

            //Comprobamos que el receptor no lo tenga bloqueado
            if ( $emisor->bloqueadors->where('id', $receptor->id )->count() ){
            return;
            }

            if ( $emisor->matches()->where('id', $receptor->id ) ){

            //Extrae el último token vigente del receptor
            $receptor_token = $receptor->tokens->where('revoked',false)->sortByDesc('updated_at')->first();

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



            $message = new \App\Message();

            $message->emisor = $emisor->id;
            $message->receptor = $receptor->id;
            $message->receptor_token = $receptor_token; //Alternativa sin almacenar $message["receptor_token"] = $receptor_token;
            $message->texto = $event->message->texto;

            $message->save();

            $time = $message->created_at;
            $message["time"] = $time->toTimeString();

            Redis::publish('messages', json_encode([ $message ]) );

        } else {

            

            }
      
        }
    }
}
