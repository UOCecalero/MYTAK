<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Evento;
use App\Ticket;
use App\Price;
use Stripe\{Stripe, Charge, Customer};

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

    public function show(User $user)
    {

     $tickets = $user->ticket;

     if ($tickets->isEmpty()){ return null; }
        else{ 






            return $eventos; }
     
    }

    //Esta función no estara de momento públicamente publicada en la apiRest
    public function store(User $user, Price $type, $num_tickets, $card_token )
    {
        //$data= $request->json()->all();

        Stripe::SetApiKey( config('services.stripe.secret') );

        //$tickets = $data['numtickets'];
        $cash = $num_tickets * $type->precio;

        $price = Price::find(ticket->price_id);
        $evento = Evento::find($price->evento_id);

        $charge = Charge::Create([

            // 'customer' => $user->customer->id,
            'description' =>$evento->nombre,
            //'source' => $data['token'],
            'source' => $card_token
            'amount' => $cash,
            'currency' => 'eur',
            'metadata' => array("price_id" => $type->id, "price_name" => $type->name  )
            
            ])

        if ($charge){

        // Creamos el ticket
        $ticket = new Ticket;

        //creamos un radom para la transacción
        $random = random_int(1,65535);

        //Generamos el hash
        $concat = $random.$ticket->id.$type->id.$ticket->created_at.$evento->id.$user->id;
        $hash = hash("md5", $concat);
        //este código qr hará una petición a una dirección que nos devolverá una página en verde o rojo en función de si el ticket es válido o no, ademas de su fecha y evento en grande. Podría incluir otros metadatos del comprador, como su nombre, la foto, etc...

        $qr = QrCode::size(100)->generate( config('services.url')./$hash)

        $ticket->random = $random;
        $ticket->evento_id = $evento->id;
        $ticket->user_id = $user->id;
        $ticket->price_id = $type->id;
        $ticket->token = $token;
        $ticket->qr = $qr;
        $ticket->hash = $hash
        $ticket->used_times = 0;
        $ticket->used_limit = 1;

        $ticket->save();

        return 1;
 

        } else { 

            return 0;
        }


        public function validateTicket($hash)
    {

        $ticket = Ticket::where('hash', $hash);


        if($ticket){

        $price = $ticket->price;
        $ticket_type = $price->name;
        $ticket_price = $price->precio;
        $evento = Evento::find($ticket->evento_id);
        $nombre_evento = $evento->nombre;
        $fecha_evento = $evento->event_ini;
        $user = $ticket->user;
        $nombre_user = $user->name;
        $usos = $used_limit - $used_times;


            if($ticket->used_times < $ticket->used_limit){

            $ticket->used_times ++;
            $ticket->save();



            $validation = "ACEPTADO";

            return view (layouts.validation, compact('validation','ticket_type','ticket_price','nombre_evento','fecha_evento','nombre_user','usos') )



             } else {

            $validation = "AGOTADO";

            return view (layouts.validation, compact('validation','ticket_type','ticket_price','nombre_evento','fecha_evento','nombre_user','usos') )


              }

            
        } else {

            $validation = "TICKET INVÁLIDO";

            return view (layouts.validation, compact('validation') )


        }

    

    }





}
