<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(UsersTableSeeder::class);
    	//Creamos los usuarios
    	factory(App\User::class, 50)->create()
    		->each(function($usuario){
                $usuario->gender = $gender = $this->getGender(); //male, female, both
                $usuario->genderpreference = $genderpreference = $this->getGenderPreference($gender); //male, female, both
                $usuario->save();
                print("User created \n");
    			//Cada usuario crea una empresa
    			$usuario->empresa()->save(factory(App\Empresa::class)->make())
    				->each(function($empresa){
                        print("Empresa created: ".$empresa."\n");
    					//Cada empresa crea sus eventos
    					$empresa->eventos()->saveMany(factory(App\Evento::class, 2)->make())
    						->each(function($evento){ 
    							print("Evento created: ".$evento." \n");
                                //Cada evento crea sus entradas (prices)
    							$evento->prices()->saveMany(factory(App\Price::class, 1)->make())
                                ->each(function($price){ 
                                    print("Price created: ".$price." \n");
                                 });

    						});
    				});
    		});

    	//Creamos los tickets
    	factory(App\Ticket::class, 1000)->create()
    		//Calculamos el hash de cada ticket
    		->each(function($ticket){
            $ticket->user_id = $this->getRandomUserId(1);
            $ticket->price_id = App\Price::inRandomOrder()->first()->id;
            $ticket->evento_id = App\Price::findOrFail($ticket->price_id)->evento->id;
            $ticket->random = random_int(1,65535);
    		$concat = $ticket->random.$ticket->id.$ticket->price_id.$ticket->created_at.$ticket->evento_id.$ticket->user_id;
    		$ticket->hash = hash("md5", $concat);
    		$ticket->save();
            print("Ticket created: ".$ticket." \n");
    		});


    	//Creamos los mensajes
    	factory(App\Message::class, 5000)->create()
        ->each(function($message){
            $message->emisor = $this->getRandomUserId(1);
            $message->receptor = $this->getRandomUserId($message->emisor);
            $message->save();
            print("Message created: ".$message." \n");
        });

        //Creamos los matches
        
        App\Evento::all()
        ->each(function($evento){
            //Comprobamos si el evento tiene algun ticket
            if ($evento->tickets->count() > 1){
                //Gurdamos los usuarios de cada ticket
                $usersCollection = collect([ ]);
                $evento->tickets->each(function($ticket) use ($usersCollection){
                    $usersCollection->push($ticket->user);
                });
                //Eliminamos los repetidos
                $usersCollection = $usersCollection->unique();
                //Numero maximo de matches por evento
                $maxMatchPerEvent = 3;
                //Numero de match que queremos crear, si es posible con los tickets existentes.
                $matchNumber = 20;
                while ($usersCollection->count() > 1 && $maxMatchPerEvent > 0 && $matchNumber > 0){

                    //Cogemos los dos últimos y los sacamos de la coleccion
                    $user1_id = $usersCollection->pop()->id;
                    $user2_id = $usersCollection->pop()->id;
                    
                    //Creamos el Match
                    factory(App\Match::class)->create([
                        'usuario1_id' => $user1_id,
                        'usuario2_id' => $user2_id,
                        'evento_id' => $evento,
                    ])
                    ->each(function($match){
                        print("Match created: ".$match." \n");    
                    });

                    //Decreamentamos los contadores
                    $maxMatchPerEvent --;
                    $matchNumber --;
                }
            }
        });
    	// //Creamos los matches
    	// factory(App\Match::class, 20)->create()
     //    ->each(function($match){
     //        $match->evento_id = $this->getRandomEventoId();
     //        // $match->usuario1_id = $this->getUserWithTickets();
     //        $match->usuario1_id = $this->getUserFromEvento($match->evento_id, -1);
     //        $match->usuario2_id = $this->getUserFromEvento($match->evento_id, $match->usuario1_id);
     //        $match->save();
     //        print("Match created: ".$match." \n");
     //    });


    	// factory(App\Bloqueadors::class, 200)->create([

    	// 'user_id' => >$this->getRandomUserId(),
     //    'bloqueador_id' => $this->getRandomUserId($emisor) ,

    	// ]);
       

    }

    private function getRandomUserId($userId){
    	
    	//Evita que emisor y receptor sean el mismo
    	$user = $userId;
    	while( $user == $userId ) {
    		$user = App\User::inRandomOrder()->first()->id;
    	}
    	return $user;
    }


    //Devuelve un usuario que tiene algun ticket
    private function getUserWithTickets(){
        $user = App\Ticket::inRandomOrder()->first()->user;
        return $user->id;

    }

    private function getRandomEventoId(){
        
        $ticket = App\Ticket::inRandomOrder()->first();
        // $ticket = $user->tickets->random();
        $evento = $ticket->evento;
        return $evento->id;
    }

    //Esta funcion devuelve un usuario para el mismo evento (que no sea él mismo)
    private function getUserFromEvento($eventoId, $userId){

    	$user2Id = $userId;
        $evento = App\Evento::findOrFail($eventoId);
    	while ( $user2Id == $userId) {
    		$userId = $evento->tickets->random()->user->id;
    	}
    	return $userId;
    }

    //Esta funcion devuelve un usuario para el mismo evento (que no sea él mismo)
    private function getGender(){
        
        $gender =  array_random(['male', 'female']);
        return $gender;
    }

    private function getGenderPreference($gender){
        
        if ($gender == 'male') { 
            $genderpreference = 'female'; 
        } else {  
            $genderpreference ='male';
        }
        return $genderpreference;
    }
    
}
