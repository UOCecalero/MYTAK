<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Evento;
use App\Match;
use App\Empresa;
use App\Bloqueado;
use App\User;
//use Stripe\{Stripe, Charge, Customer}


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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exists($fbid)
    {
        $user = User::where('FBid', $fbid )->get();

        $resp = count($user);

        if ($resp == 0) { return $resp; }

        else { return $user[0]->id; }

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function email($fbid)
    {
        $user = User::where('FBid', $fbid )->get();


        return $user->email;
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
        $data= $request->json()->all();

        $user = new User;

        $user->FBid = $data['FBid'];
        //$user->last_connection =$data('last_connection');
        $user->name = $data['name'];
        $user->surnames = $data['surnames'];
        $user->gender = $data['gender'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->photo = $data['photo'];
        $user->birthdate = $data['birthdate'];
        $user->job = $data['job'];
        $user->studies = $data['studies'];
        //$user->ranking = $data('ranking'); No se puede mandar el ranking desde fuera
        $user->aceptar = $data['aceptar'];
        $user->saludar = $data['saludar'];
        $user->rechazar = $data['rechazar'];
        $user->destacado_ini = $data['destacado_ini'];
        $user->destacado_fin = $data['destacado_fin'];
        $user->location = $data['location'];

        // $user->FBid = request->FBid;
        // //$user->last_connection =$request->last_connection;
        // $user->name = $request->name;
        // $user->surnames = $request->surnames;
        // $user->gender = $request->gender;
        // $user->email = $request->email;
        // $user->password = Hash::make($request->password);
        // $user->photo = $request->photo;
        // $user->birthdate = $request->birthdate;
        // $user->job = $request->job;
        // $user->studies = $request->studies;
        // //$user->ranking = $request->ranking; No se puede mandar el ranking desde fuera
        // $user->aceptar = $request->aceptar;
        // $user->saludar = $request->saludar;
        // $user->rechazar = $request->rechazar;
        // $user->destacado_ini = $request->destacado_ini;
        // $user->destacado_fin = $request->destacado_fin;
        // $user->location = $request->location;
    

        $user->save();

        return $user;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($fbid)
    {
        // $user = User::where('FBid', $fbID )->get();

        // return $user;

        $user = User::find($fbid);
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
        $eventos = DB::table('tickets')
                    ->where('tickets.user_id', '=', $user->id)
                    ->join('prices', 'tickets.price_id', '=', 'prices.id')
                    ->join('eventos','prices.evento_id','=','eventos.id')
                    ->select('tickets.id as ticketid','eventos.*','prices.name','prices.description','prices.precio','tickets.qr')
                    ->get();

        if ($eventos->isEmpty()){ return null; }
        else{ return $eventos; }

    }

    // /**
    //  * Añadir un evento al listado de eventos de un user concreto
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function addevento(User $user, Evento $evento)
    // {   
    //     //Si el evento ya ha sido añadido/comprado, devuelve un 0;
       
    //     if (! $user->eventos->contains($evento)) {

    //             if ($evento->price > 0){

    //             $charge = Charge::Create([

    //              'customer' => $user->customer->id,
    //              'amount' => $evento->price,
    //              'currency' => 'eur',
    //              'description' => 'Tunait: '.$evento->nombre.' '.$evento->event_ini

    //              ]);

    //             //Aqui hay que implementar el código que guarda el token ($charge->id) del cargo en la pivot table que relaciona usuarios y eventos

    //             //Aqui hay que implementar el envío de un email con el recibo donde conste el código QR que no es mas que el token de pago codificado.
    //             }
            
    //         $user->eventos()->attach($evento);
    //         return 1;

    //     }

    //     return 0;
    // }

    //  /**
    //  * Eliminar un evento del listado de eventos de un user concreto
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function delevento(User $user, Evento $evento)
    // {
    //     $result = $user->eventos->where('id',$evento->id);

    //     if( $result->isEmpty() != 'true')
    //     {
        
    //     $user->eventos()->detach($evento);
    //     return 1;
        
    //     } else return 'Este usuario no tiene el evento: '. $evento ;

    // }

     /**
     * Create a customer id and add it to the database 
     *
     * @return \Illuminate\Http\Response
     */
    public function newUserPurchase(User $user, $token)
    {
       

            $user = User::find($user);
            $user->customer_id = $customerid;
            return 1;        

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

        if ($matches->isEmpty()){ return null; }
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
        else return null;
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

        return 1;

    }

    /*** 
     * Mostrar la empresa(s) de un user
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function showEmpresa(User $user)
    {
        
        
        return $user->empresa;

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
}