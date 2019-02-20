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
    	factory(App\User::class, 100)->create([
            'gender' =>  $gender = $this->getGender() , //male, female, both
            'genderpreference' =>  $genderpreference = $this->getGenderPreference($gender), //male, female, both
        ])
    		->each(function($usuario){ 
    			//Cada usuario crea una empresa
    			$usuario->empresa()->save(factory(App\Empresa::class)->make()
    				->each(function($empresa){
    					//Cada empresa crea sus eventos
    					$empresa->eventos()->saveMany(factory(App\Evento::class, 100)->make()
    						->each(function($evento){ 
    							//Cada evento crea sus entradas (prices)
    							$evento->prices()->saveMany( factory(App\Price::class)->make());
    						}));
    				}));
    		});

    	//Creamos los tickets
    	factory(App\Tickets::class, 200)->create([
    		'random' => $random = random_int(1,65535),
	        'user_id' => $user = $this->getRandomUserId(),
	        'price_id' => $price = $this->getRandomPrice(),
	        'evento_id' => $evento = $price->evento()->id,
	        //'hash' => Se tiene que calcular a posteriori mediante una función,
    	])
    		//Calculamos el hash de cada ticket
    		->each(function($ticket){

    			$concat = $random.$ticket->id.$type->id.$ticket->created_at.$evento->id.$user->id;
    			$ticket->hash = hash("md5", $concat);
    			$ticket->save();
    		});


    	//Creamos los mensajes
    	factory(App\Message::class, 10000)->create([

    	 'emisor' => $emisor = $this->getRandomUserId(null),
         'receptor' => $this->getRandomUserId($emisor),
    	]);

    	//Creamos los matches
    	factory(App\Match::class, 200)->create([

    	'usuario1_id' => $userId = $this->getRandomUserId(),
        'evento_id' => $eventoId =$this->getRandomEventoId($userId),
        'usuario2_id' => $this->getUserFromEvento($eventoId, $userId),

    	]);

    	factory(App\Match::class, 200)->create([

    	'usuario1_id' => $userId = $this->getRandomUserId(),
        'evento_id' => $eventoId =$this->getRandomEventoId($userId),
        'usuario2_id' => $this->getUserFromEvento($eventoId, $userId),

    	]);

    	// factory(App\Bloqueadors::class, 200)->create([

    	// 'user_id' => >$this->getRandomUserId(),
     //    'bloqueador_id' => $this->getRandomUserId($emisor) ,

    	// ]);

    }

    private function getRandomUserId($userId){
    	
    	//Evita que emisor y receptor sean el mismo
    	$user = $userId;
    	while( $user == $userId ) {
    		$user = \App\User::inRandomOrder()->first();
    	}
    	return $user->id;
    }

    private function getRandomEventoId($userId){
    	
    	$user = \App\User::findOrFail($userId);
    	$eventos = $user->tickets()->evento()->get();
    	$evento = $eventos::inRandomOrder()->firstOrFail();
    	return $evento->id;
    }

    private function getRandomPrice(){

    	$price = \App\Price::inRandomOrder()->firstOrFail();
    	return $price;
    }

    //Esta funcion devuelve un usuario para el mismo evento (que no sea él mismo)
    private function getUserFromEvento(Evento $evento, User $user){
    	$user2 = $user;
    	while ( $user2 == $user) {
    		$user = $evento->users()->inRandomOrder()->firstOrFail();
    	}
    	return $user2;
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