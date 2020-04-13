<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Evento;
use App\Ticket;
use App\Price;
use App\Match;
use Carbon\Carbon;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;
use Stripe\{Stripe, Charge, Customer};
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class PurchasesController extends Controller
{
    // public function newCustomer(User $user, Request $request)
    // {

    // 	$data= $request->json()->all();

    // 	Stripe::SetApiKey( config('services.stripe.secret') );

    // 	$cus = Customer::create([

    //      'email' => $user->email,
    // 		'source' => $data['stripeToken']

    // 		]);

    // 	$user->customer = $cus;
    // 	$user->save();
    // 	return 1;
    // }

    public function ordermembers( Ticket $ticket/*, $position*/) //Se ha modificado y devuelve el perfil actual y el siguiente, ya que al evaluar y hacer de nuevo la llamada sin ningun buffer, los ya evaluados desaparecen y solo nos interesan los dos primeros.
    {
        //1. Buscamos los miembros que tienen tickets a dicho evento
        //2. Eliminamos los miembros con los que ya hemos echo match (positivo o negativo)
        //3. Eliminamos los que no son del género deseado
        //5. Hacemos el filtrado por edad
        //6. Evaluamos

        $user = Auth::user();
        $evento = $ticket->evento;
        $tickets = $evento->tickets;
        $users = collect([]);

        foreach ($tickets as $ticket) {

            $users->push($ticket->user);
             
         };



        //Como puede que un user tenga varios tickets al mismo evento. Eliminamos los users duplicados
        $users = $users->unique();
        $users = $users->keyBy('id');
        $users = $users->forget($user->id);


        if ( $users->count() == 0 ) { return $users; } //Si no hay usuarios devuelve la colección vacía



        //Buscamos todos los match anteriores (positivos y negativos) a dicho evento.
        $matches = Match::where('usuario1_id', $user->id)->where('evento_id', $evento->id );     
        $matched = $matches->pluck('usuario2_id');
        $matched = User::find($matched); //Le podemos pasar un array a find
        $torank = $users->diff($matched);

        //Filtramos por género
        $genderpref = $user->genderpreference;
        $inagepreference = $user->inagepreference;
        $outagepreference = $user->outagepreference;
        
        $torank = $torank->filter(function ($item) use ($genderpref) {
                        if ( $item->gender == $genderpref ) {
                            return $item;
                        } 
                    });

        //Filtramos por edad

        $torank = $torank->filter(function ($item) use ($inagepreference, $outagepreference) {

                        if ( isset($item->birthdate) ) { return $item; } //Esto se ha añadido dado que no hay birthdate de momento
                        $now = Carbon::now();
                        $birthdate = new Carbon($item->birthdate);
                        $edad = $birthdate->diffInYears($now);
                        if ( $inagepreference <= $edad && $edad >= $outagepreference ) {
                            return $item;
                        } 
                    });

        //Rankeo de actividad y sociabiliadad
        $torank->map(function($element){
	    
            $element->archives->where('type',1); //Crea un campo pictures donde estan las url de las imágenes


            $current = new Carbon();

            $last = new Carbon($element->last_connection);

            //Rankeo de actividad
            $diff = $current->diffInHours($last);
                
                if ($diff < 1){
                    $element['timerank'] = 80;

                } else if ($diff < 3){
                    $element['timerank'] = 70;

                } else if ($diff < 6){
                    $element['timerank'] = 60;

                } else if ($diff < 12){
                    $element['timerank'] = 50;

                } else if ($diff < 24){
                    $element['timerank'] = 40;

                } else if ($diff < 72){
                    $element['timerank'] = 30;

                } else if ($diff < 168){
                    $element['timerank'] = 20;
                }
                else if ($diff < 360){
                    $element['timerank'] = 10;

                } else { $element['timerank'] = 0; }

            //Rankeo de sociabiliadad
            if ( ($element->aceptar + $element->saludar + $element->rechazar) > 0){

            $element['socialrank'] =  ($element->aceptar + $element->saludar) / ($element->aceptar + $element->saludar + $element->rechazar);
            $element['socialrank'] = $element['socialrank'] * 10;

            } else { $element['socialrank'] = 0; }

            $element['rank'] = $element['timerank'] + $element['socialrank'];

            $upload = User::find($element->id);
            $upload->ranking = ( $element->ranking + $element['rank'] ) /2;
            $upload->save();

            //Si el usuario es premium divide el factor entre 10 y le suma 900 para dejarlo entre los primeros
            $first = new Carbon($element->destacado_ini);
            $second = new Carbon($element->destacado_fin);
            $element['premium'] = Carbon::now('Europe/Madrid')->between($first, $second);

            if ($element['premium']){
                
                $element['rank'] = $element['rank']/10 + 90;

            }

            return $element;

        });

            $sorted = $torank->sortByDesc(function($element){


                return $element->rank;
            });

            

            //return $sorted->values()->get($position - 1); //La resta es para que empiece a indexar en 1
            return $sorted->slice(0,2)->values(); //Devolvemos la primera y siguiente posición de las que no se han evaluado. Al evaluar, se lanza de nuevo la petición para volver a evaluar y poner la anterior delante.

    }

    public function show()
    {

     $user = Auth::user();

     $tickets = $user->tickets;

     if ($tickets->isEmpty()){ return []; }
    
        else{ 

            return \App\Helpers\General\CollectionHelper::paginate($tickets); 
        }
     
    }

    //Esta función no estara de momento públicamente publicada en la apiRest
    public function store( Price $type, $num_tickets, $card_token )
    {
        //$data= $request->json()->all();

        $user = Auth::user();

        //Calculamos las entradas restantes que quedarán si se completa el proceso
        //Si el numero de entradas restantes es menor que 0 significa que no hay entradas suficientes para esa demanda y devuelve un numero negativo con el numero de entradas que faltan. Se puede usar ese numero para indicar al usuario las que faltan.
        $entradas_restantes = $num_tickets - $type->availables;

        if( $entradas_restantes < 0) { return $entradas_restantes;  }

        Stripe::SetApiKey( config('services.stripe.secret') );

        //$tickets = $data['numtickets'];
        $cash_total = $num_tickets * $type->precio;

        //$price = Price::find($ticket->price_id); ??????????????? De donde sale $ticket??
        //$evento = Evento::find($type->evento_id);
        $evento = $type->evento;

        // try {

            $charge = Charge::Create([

                // 'customer' => $user->customer->id,
                'description' =>$evento->nombre,
                //'source' => $data['token'],
                'source' => $card_token,
                'amount' => $cash_total,
                'currency' => 'eur',
                'metadata' => array( "user_id" => $user->id, "user_name" => $user->name." ".$user->surnames, "price_id" => $type->id, "price_name" => $type->name, "price" => $type->precio, "num_tickets" => $num_tickets , "event" => $evento->id, "event_name" => $evento->nombre )
                
                ]);

        // } catch(\Stripe\Error\Card $e) {
        //       // Since it's a decline, \Stripe\Error\Card will be caught
        //       $body = $e->getJsonBody();
        //       $err  = $body['error'];

        //       throw new Exception("Error Processing Request", 1);
              
        //       print('Status is:' . $e->getHttpStatus() . "\n");
        //       print('Type is:' . $err['type'] . "\n");
        //       print('Code is:' . $err['code'] . "\n");
        //       // param is '' in this case
        //       print('Param is:' . $err['param'] . "\n");
        //       print('Message is:' . $err['message'] . "\n");
        //     } catch (\Stripe\Error\RateLimit $e) {
        //       // Too many requests made to the API too quickly
        //     } catch (\Stripe\Error\InvalidRequest $e) {
        //       // Invalid parameters were supplied to Stripe's API
        //     } catch (\Stripe\Error\Authentication $e) {
        //       // Authentication with Stripe's API failed
        //       // (maybe you changed API keys recently)
        //     } catch (\Stripe\Error\ApiConnection $e) {
        //       // Network communication with Stripe failed
        //     } catch (\Stripe\Error\Base $e) {
        //       // Display a very generic error to the user, and maybe send
        //       // yourself an email
        //     } catch (Exception $e) {
        //       // Something else happened, completely unrelated to Stripe
        //     }

        // if ($charge) {

            for ($i = 0; $i < $num_tickets; $i ++){ // Lo que hace es crear un qr por cada ticket pero un solo recargo 


                // Creamos el ticket
                $ticket = new Ticket();

                //creamos un radom para la transacción
                $random = random_int(1,65535);

                //Generamos el hash
                $concat = $random.$ticket->id.$type->id.$ticket->created_at.$evento->id.$user->id;
                $hash = hash("md5", $concat);
                //este código qr hará una petición a una dirección que nos devolverá una página en verde o rojo en función de si el ticket es válido o no, ademas de su fecha y evento en grande. Podría incluir otros metadatos del comprador, como su nombre, la foto, etc...

                $qr = QrCode::size(300)->generate( env('URL') ."/".$hash); //env hace referencia al archivo .env . HAY QUE ACTUALIZAR LA URL!!!

                $ticket->random = $random;
                $ticket->evento_id = $evento->id;
                $ticket->user_id = $user->id;
                $ticket->price_id = $type->id;
                //$ticket->card_token = $card_token; //Si roban los tokens de la tarjeta podrían hacer recargos (aunque son de un solo uso)
                $ticket->qr = $qr;
                $ticket->hash = $hash;
                $ticket->used_times = 0;
                $ticket->used_limit = 1;

                $ticket->save();

                $type->availables --;

            }

        return $charge;
 
        // } else {   return 0;   }
    }

        public function validateTicket($hash)
    {

        $ticket = Ticket::where('hash', $hash);


        if($ticket){

        $price = $ticket->price;
        $ticket_type = $price->name;
        $ticket_price = $price->precio;
        $evento = $ticket->evento;
        $nombre_evento = $evento->nombre;
        $fecha_evento = $evento->event_ini;
        $user = $ticket->user;
        $nombre_user = $user->name;
        $usos = $used_limit - $used_times;


            if($ticket->used_times < $ticket->used_limit){

            $ticket->used_times ++;
            $ticket->save();



            $validation = "ACEPTADO";

            return view (layouts.validation, compact('validation','ticket_type','ticket_price','nombre_evento','fecha_evento','nombre_user','usos') );



            } 

            else{

            $validation = "AGOTADO";

            return view (layouts.validation, compact('validation','ticket_type','ticket_price','nombre_evento','fecha_evento','nombre_user','usos') );


              }

            
        } else {

            $validation = "TICKET INVÁLIDO";

            return view (layouts.validation, compact('validation') );


        }

    

    }

}
