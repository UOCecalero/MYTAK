<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Evento;
use App\Match;
use App\Empresa;
use App\Bloqueado;
use App\User;
use App\Ticket;
use App\Archive;
use Carbon\Carbon;
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

        return $users::paginate();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function me()
    {   
        $user = Auth::user();
        $user->last_connection = Carbon::now('Europe/Madrid');
        $user->save();
        $birthdate = $user->birthdate;
        $user['age'] = (string)Carbon::createFromFormat('Y-m-d',$birthdate)->age;
        return \App\Helpers\General\CollectionHelper::paginate(collect([$user]));   
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function exists($token, $devicetoken)
    {   
        //Hace una llamada a Facebook para comprobar que el token es bueno
        $fb = new \Facebook\Facebook([
              'app_id' => '261908097712873',
              'app_secret' => 'e783ded20404501e301c13b7c2afc71f',
              'default_access_token' => $token,
              'default_graph_version' => 'v3.3',
              ]);


        //Con lo que devuelve Faecbook podemos hacer una llamada para extraer datos
        try {
          $resp = $fb->get('me?fields=id,first_name,last_name,gender,picture.height(480),email,birthday' );
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
          // When Graph returns an error
          echo 'Graph returned an error: ' . $e->getMessage();
          exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
          // When validation fails or other local issues
          echo 'Facebook SDK returned an error: ' . $e->getMessage();
          exit;
        }

        $data = $resp->getDecodedBody();
        $collection = User::where('email', $data['email'] )->get();

        $number = $collection->count();


        if ($number > 1) { 
          throw new Exception("Error: Many users with this FBid", 1);
        }

        //Si el usuario no existe en el sistema
        if ( ! $number ) { 

            // {
            //   "id": "10155137218645472",
            //   "first_name": "Edu",
            //   "last_name": "Calero Rovira",
            //   "gender": "male",
            //   "picture": {
            //     "data": {
            //       "height": 480,
            //       "is_silhouette": false,
            //       "url": "https://scontent.xx.fbcdn.net/v/t1.0-1/14433114_10157499718300472_2854134556661342048_n.jpg?oh=a95fa3725b47303daca32b4b8c741bd3&oe=5B2F1AC2",
            //       "width": 480
            //     }
            //   },
            //   "email": "educalerorovira@gmail.com",
            //   "birthday": "03/15/1985"
            // }

            $user = new User();

            $user->FBid = $data['id'];
            //$user->last_connection =$data('last_connection');
            $user->name = $data['first_name'];
            $user->surnames = $data['last_name'];
            $user->gender = $data['gender'];
            $user->email = $data['email'];
            //$user->password = Hash::make($data['password']);
            if ($devicetoken) { $user->devicetoken = $devicetoken; }
            $user->birthdate = Carbon::createFromFormat('m/d/Y',$data['birthday']);
            // $user->job = $data['job'];
            // $user->studies = $data['studies'];
            // $user->aceptar = $data['aceptar'];
            // $user->saludar = $data['saludar'];
            // $user->rechazar = $data['rechazar'];
            // $user->destacado_ini = $data['destacado_ini'];
            // $user->destacado_fin = $data['destacado_fin'];
            // $user->lat = $data['lat'];
            // $user->lng = $data['lng'];
            if ( $data['gender']== 'male') {  $user->genderpreference = 'female'; }  
            else { $user->genderpreference = 'male'; }

            /******************************** Calculamos el avg **********************/
            $collection = User::all(); 
            $avg = $collection->avg('ranking'); 
            $avg = $avg + $avg / 2;

            if ($avg > 89) { $avg = 89; }

            if ($avg == 0) { $avg = 45; }

            /***************************************************************************/

            $user->ranking = $avg;
            
            $user->save();

         }

         //En este else se entra si hay exactamente un usuario con ese email
          else { $user = $collection[0]; }

        //Crea un accessToken nuevo tanto si existe ya uno como si no. Si existe alguno lo elimina.
        if( isset( $user->tokens[0]) ){ $user->tokens[0]->delete(); };
        $accesstoken = $user->createToken('accessToken')->accessToken;
        
	//Si recibe un deviceToken para las notificaciones push lo guarda. Si hay uno antiguo, sobreescribe.
	if ($devicetoken) { $user->devicetoken = $devicetoken; }
        $user->save();
	
	
        //Si no tiene una foto guardada. Intenta guardar la que tiene en facebook siempre y cuando no sea la genérica.
	if ( !isset($user->photo) ) {

        if ( $facebookPhotoUrl = $data['picture']['data']['url']  ) {


                if ( $contents = file_get_contents($facebookPhotoUrl) ){

                        //De la url que devuelve, coge como nombre la cadena que hay despues del igual y le añade .jpg al final
                        $name = substr($facebookPhotoUrl, strrpos($facebookPhotoUrl, '=', -1) + 1).".jpg";
                        if ( Storage::disk('public')->put('avatars/'.$name, $contents) ){

                          //$path = asset('storage/'.$name); //asset y scure_asset no se de donde cogen la URL del server
                          $path = env('URL')."storage/avatars/".$name;

                          $archive = new Archive;
                          $archive->user_id = $user->id;
                          $archive->path = $path;
                          $archive->position = 1;
                          $archive->type = 1;
                          $archive->save();

                          $user->photo = $path;
                          $user->save();
                        }
                        

                    }
            }

	}
        return $accesstoken;
        


        // $user = User::where('FBid', $fbid )->get();

        // $resp = count($user);

        // if ($resp == 0) { return $resp; }

        // else { return $user[0]->id; }

    }

    // /**
    //  * Display a listing of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function email($fbid)
    // {
    //     $user = User::where('FBid', $fbid )->get();


    //     return $user->email;
    // }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function create()
    // {
    //     //Esta funcion se usa para crear y para modificar un usuairo 
    //     $data= $request->json()->all();

    //     $user = new User;

    //     $user->FBid = $data['FBid'];
    //     //$user->last_connection =$data('last_connection');
    //     $user->name = $data['name'];
    //     $user->surnames = $data['surnames'];
    //     $user->gender = $data['gender'];
    //     $user->email = $data['email'];
    //     $user->password = Hash::make($data['password']);
    //     $user->photo = $data['photo'];
    //     //$user->birthdate = $data['birthdate'];
    //     $user->job = $data['job'];
    //     $user->studies = $data['studies'];
    //     $user->aceptar = $data['aceptar'];
    //     $user->saludar = $data['saludar'];
    //     $user->rechazar = $data['rechazar'];
    //     $user->destacado_ini = $data['destacado_ini'];
    //     $user->destacado_fin = $data['destacado_fin'];
    //     $user->lat = $data['lat'];
    //     $user->lng = $data['lng'];
    //     $user->genderpreference = $data['genderpreference'];

    //     $user->ranking = function(){ 

    //                         $collection = App\User::all(); 
    //                         $avg = $collection->avg('ranking'); 
    //                         $avg = $avg + $avg / 2;

    //                         if ($avg > 89) { $avg = 89; }

    //                         if ($avg == 0) { $avg = 45; }

    //                         return $avg;  
    //                     };
        
    //     $user->save();

    //     return $user;
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {    
        $user = auth()->user();
        
        //Esta funcion se usa para crear y para modificar un usuairo 
        $data= $request->json()->all();

        //$user->FBid = $data['FBid'];
        //$user->last_connection =$data('last_connection');
        $user->name = $data['name'] ?? $user->name;
        $user->surnames = $data['surnames'] ?? $user->surnames;
        $user->gender = $data['gender'] ??  $user->gender;
        $user->lema = $data['lema'] ??  $user->lema;
        //$user->email = $data['email'];
        $user->password = Hash::make($data['password'] ?? $user->password);
        $user->photo = $data['photo'] ?? $user->photo;
        $user->birthdate = $data['birthdate'] ?? $user->birthdate;
        $user->job = $data['job'] ?? $user->job;
        $user->studies = $data['studies'] ?? $user->studies;
        //$user->aceptar = $data['aceptar'];
        //$user->saludar = $data['saludar'];
        //$user->rechazar = $data['rechazar'];
        //$user->destacado_ini = $data['destacado_ini'];
        //$user->destacado_fin = $data['destacado_fin'];
        $user->lat = $data['lat'] ?? $user->lat;
        $user->lng = $data['lng'] ?? $user->lng;
        $user->genderpreference = $data['genderpreference'] ?? $user->genderpreference;
        $user->inagepreference  = $data['inagepreference'] ?? $user->inagepreference;
        $user->outagepreference = $data['outagepreference'] ?? $user->outagepreference;
        $user->eventdistance = $data['eventdistance'] ?? $user->eventdistance;

        $user->save();

        return $user;
    }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($fbid)
    // {
    //     // $user = User::where('FBid', $fbID )->get();

    //     // return $user;

    //     $user = User::find($fbid);
    //     return $user;
    // }

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
    public function destroy(User $user)
    {
        $user=Auth::user();
        $user->delete();

        return 1;
    }


    /**
     * Listado de eventos comprados de un user concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function userevents(Array $options = [])
    {   

      $user = Auth::user();

      $eventos = DB::table('tickets')
                  ->where('tickets.user_id', '=', $user->id)
                  ->join('prices', 'tickets.price_id', '=', 'prices.id')
                  ->join('eventos','prices.evento_id','=','eventos.id')
                  ->select('tickets.id as ticketid','eventos.id as eventoid', 'eventos.creator', 'eventos.nombre','eventos.photo','eventos.event_ini','eventos.event_fin','eventos.aforo','eventos.location_name','eventos.lat','eventos.lng', 'prices.name as type','prices.description','prices.precio','tickets.qr', 'tickets.hash')
                  ->get();

        //if ($eventos->isEmpty()){ abort(404,'No hay tickets'); }
        if ($eventos->isEmpty()){ return \App\Helpers\General\CollectionHelper::paginate(collect()); }
        return \App\Helpers\General\CollectionHelper::paginate($eventos, 10);

    }

     /**
     * Devuelve la posición ordenada del evento en función de su puntuación y la distancia.
     *
     * @param  int  $position
     * @param  int  $distance
     * @return \Illuminate\Http\Response
     */
    public function orderevents(Array $options = [])
    {   

        $user = Auth::user();
        $lat = $user->lat;
        $lng = $user->lng; 
        $distance = $user->eventdistance ?? 25;
        

        //if (empty($distance)){ $distance = 25; /** 25Km distancia por defecto -> varable en la App Móvil**/ }

            //Esta version antigua de filtered, no devolvía los prices de cada evento

            // $filtered = Evento::all()->filter(function ($evento) use ($lat, $lng, $distance){
            //                 $actual = 3959 * acos(
            //                 cos(deg2rad($lat)) * cos(deg2rad($evento->lat))
            //                 * cos(deg2rad($evento->lng) - deg2rad($lng))
            //                 + sin(deg2rad($lat)) * sin(deg2rad($evento->lat))
            //             );

            $all_events = Evento::with('prices')->get();

            //if ($distance > 150) { $distance = 150}

            $filtered = $all_events->filter(function ($evento) use ($lat, $lng, $distance){
                            $actual = 3959 * acos(
                            cos(deg2rad($lat)) * cos(deg2rad($evento->lat))
                            * cos(deg2rad($evento->lng) - deg2rad($lng))
                            + sin(deg2rad($lat)) * sin(deg2rad($evento->lat))
                        );

                        $evento['distance'] = $actual; //Guarda la distancia en una nueva propiedad del evento
                        $evento['distfactor'] = round((90 /(1 + ($actual / $distance))) * 10);

                         return $distance > $actual;
                        });

            // //Filtra todos los eventos que ya han finalizado
            // $filtered = $filtered->filter(function($evento){
            //             $fin = new Carbon($evento->event_fin);
            //             return $fin->isfuture();

            //             }) 

        // if ($day){} /**Aqui falta hacer el filtrado por dia. **/

            $current = Carbon::now('Europe/Madrid'); //Calcula el tiempo actual UTC para hacer comparaciones
            
            $filtered->map(function($element) use ($current){

                $element['popfactor'] = ((count($element->tickets) / ($element->aforo)) * 450 ) - 450;
                $event_ini = new Carbon($element->event_ini);
                
                $diff = $current->diffInHours($event_ini);
                
                if ($diff < 2){
                    $element['timefactor'] = 90;

                } else if ($diff < 4){
                    $element['timefactor'] = 80;

                } else if ($diff < 6){
                    $element['timefactor'] = 70;

                } else if ($diff < 12){
                    $element['timefactor'] = 60;

                } else if ($diff < 24){
                    $element['timefactor'] = 50;

                } else if ($diff < 72){
                    $element['timefactor'] = 40;

                } else if ($diff < 168){
                    $element['timefactor'] = 30;
                }
                else if ($diff < 360){
                    $element['timefactor'] = 20;

                } else { $element['timefactor'] = 10; }

                $element['points'] = $element['distfactor'] + $element['popfactor'] + $element['timefactor'];
                // //Aqui se cuenta el histórico de factor de popularidad del creador
                // $element['points'] = $element['distfactor'] - (($element['popfactor']+$element->creator->histpopfactor)/2) + element['timefactor'];


                //Si el evento es premium divide el factor entre 10 y le suma 900 para dejarlo entre los primeros
                $first = new Carbon($element->destacado_ini);
                $second = new Carbon($element->destacado_fin);
                //$element['now'] = new Carbon('Europe/Madrid');
                $element['premium'] = Carbon::now('Europe/Madrid')->between($first, $second);

                if ($element['premium']){

                    
                    $element['points'] = $element['points']/10 + 900;

                }

                return $element;

            });

            $sorted = $filtered->sortByDesc(function($element){


                return $element->points;
            });

            /******* Esta es la version antigua donde se devuelve elemento por elemento *************
            //Devuelve un error 404 si no hay mas eventos
            if( empty($sorted->values()->get($position - 1)) ) { abort(404,'No hay mas eventos'); }

            return $sorted->values()->get($position - 1); //La resta es para que empiece a indexar en 1
            *******************************************************************************************/

            /************ Esta es la version nueva donde se devuelve por bloques de X elementos *********/
            // $array = $sorted->chunk(10);
            // return $array[$position -1]->values();
            /************ Ultima versión donde se devuelve por páginas *********/
            //return \App\Helpers\General\CollectionHelper::paginate($sorted->values(),$page);
            //return $sorted->values();
            return \App\Helpers\General\CollectionHelper::paginate($sorted->values());
                
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
            return $user;        

    }

    /**
     * Mostrar los matches recíprocos de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function match()
    {    

        $user = Auth::user(); 
        $matches = $user->matches();

        if ($matches->isEmpty()){ return \App\Helpers\General\CollectionHelper::paginate(collect());}
        return matches;
        return \App\Helpers\General\CollectionHelper::paginate($matches->values(), $matches->count());
    }

    /**
     * Crear un match un match de un user. Devuelve 1 si se añade exitosamente. Sino un texto con el error
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addmatch(Ticket $ticket, User $user2, $aceptado)
    {   
        $user = Auth::user();
        
        //Comprobamos que usuario receptor del match tiene tickets para el mismo evento
        $res2 = $user2->tickets->where('evento_id', $ticket->evento->id);

        if( /* $res1->isEmpty() === 'true' OR */ $res2->isEmpty() === 'true'  )
        { 
            return 'Alguno o ambos usuarios no tienen tickets para ese evento';
        }

        //Comprobamos que no hayamos evaluado ya a ese usuario antes (en cualquier evento)
        else { 
                $res = Match::where('usuario1_id', $user->id)
                ->where('usuario2_id', $user2->id)
                ->get();

            if (  $res->isEmpty() )

                {
                $match = new Match;
                $match->usuario1_id = $user->id;
                $match->usuario2_id = $user2->id;
                $match->evento_id = $ticket->evento->id;
                $match->es_aceptado = $aceptado; //hay que asegurar que esto es un booleano
                $match->save();

                    //Evaluamos parámetros de rankeo
                    if($aceptado)
                    {

                        $user->aceptar = $user->aceptar ++;

                    } else{

                        $user->rechazar = $user->rechazar ++;
                    }
                
              //Comprobamos si hay match recíproco. Si lo hay tenemos que devolver un número mayor de 1 para que la aplicación lance un aviso

              $hayMatch = Match::where('usuario2_id', $user->id)
                ->where('usuario1_id', $user2->id)
                ->get();

                if( $hayMatch->isEmpty() ){ return 1; } else { return 2; } 

                }
            
        }
    }

    /**
     * Eliminar un match de un user con otro para todos los eventos. Devuelve 0 o el num de los matches borrados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delmatch(User $match)
    {   
        $user = Auth::user();

        $res = Match::where('usuario1_id', $user->id)->where('usuario2_id', $match->id)->get(); 

        if ($res->isEmpty()) { abort(404, "Usuario not found"); }  

            foreach ($res as $m) {
                $id = $m->id;
                Match::destroy($id);
            }

             //El numero de matches borrados (uno por evento)
             return count($res);
    }


    /**
     * Eliminar un todos los matches para un evento dado. Devuelve 0 o el numero de matches borrados.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delmatchonevento(Evento $evento)
    {
        $res = Match::where('evento_id', $evento->id)->get(); 
        if ($res->isEmpty()) { abort(404, "Macth not found");} 
        
            foreach ($res as $m) {
                
                $id = $m->id;
                Match::destroy($id);
            }
             return count($res);

    }

    /**
     * Devolver el listado de bloqueados de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueados(Int $page)
    {   
        $user = Auth::user();
        $bloqueados = $user->bloqueados;

        //if( empty($bloqueados) ){ abort(404,'No hay bloqueados'); }
        return $bloqueados::paginate();
    
    }

    /**
     * Devolver el listado de los usuarios que tienen bloqueado a un user en concreto
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bloqueadores()
    {   
        $user = Auth::user();
        $bloqueadores = $user->users;

        //if( empty($bloqueadores) ){ abort(404, 'No hay bloqueadores');}
        return $bloqueadores::paginate();
        
    }

    /**
     * Añadir un bloqueo a un user dede user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addbloqueado(User $bloqueado)
    {    
        $user = Auth::user();
        abort_unless($user->bloqueados->attach($bloqueado->id),404);

        return $bloqueado;

    }

    /**
     * Eliminar un bloqueado de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delbloqueado( User $bloqueado)
    {
        $user = Auth::user();
        abort_unless($user->bloqueados->detach($bloqueado->id),404);

        return $bloqueado;
    }


    /**
     * Mostrar la empresa(s) de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function empresa()
    {
        $user = Auth::user();
        $empresa = $user->empresa;
        if (empty($empresa)){ return \App\Helpers\General\CollectionHelper::paginate(collect());}
        return \App\Helpers\General\CollectionHelper::paginate(collect([$empresa]));
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

    } **/
}


