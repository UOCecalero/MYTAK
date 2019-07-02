<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Archive;
use App\User;

class ArchiveController extends Controller
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

    //{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $user = Auth::user();
        $avatars = $user->archives()->where('type',1)->get();
        $num_avatars = count( $avatars );

        //Restringe el máximo de avatatares por usuario a MAX_AVATARS
        if ( $num_avatars  < env('MAX_AVATARS') ){

            //$path devuelve el uri interno donde se almacena el recurso
            //Al usar php artisan storage:link se genera un enlace simbolico a la carpeta public accesible desde el exterior
            $path = request()->file('avatar')->store('public/avatars');

            //basename extrae el nombre (UUID) de la dirección que devuelve la URI anterior
            $path = env('URL')."storage/avatars/".basename($path);

            foreach ($avatars as $avatar) {
                $avatar->position ++;
                $avatar->save();
            }

            $archive = new Archive;
            $archive->user_id = $user->id;
            $archive->path = $path;
            $archive->position = 1;
            $archive->type = 1;
            $archive->save();

            $user->photo = $path;
            $user->save();

            return $archive;

        } else { 
            
            return 0; 
        }
    }

     /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Archive $archive)
    {
        $user=Auth::user();
        $avatars = $user->archives()->where('type',1)->get();

        foreach ($avatars as $avatar) {

                if ($avatar == $archive){

                    $avatar->delete();
                    return 1;

                }
            }

        return 0;
        

        
    }

    /**
     * Se intarcambian las posiciones
     *
     * @param  int  $position
     * @return int
     */
    public function changePosition(Archive $archive, $position)
    {
        $user=Auth::user();
        $avatars = $user->archives()->where('type',1)->get();
        $num_avatars = count( $avatars );

        if ($archive->position == $position) { return 0; }

        //Se intercambian las posiciones
        foreach ($avatars as $avatar) {

                    if ($avatar->position == $position) { 

                        $avatar->position = $archive->position;
                        $avatar->save();


                           }

                    if ($avatar->postion == $archive->position) { 

                        $avatar->position = $position;
                        $avatar->save(); 
                    }

                
            }


        return 1;
    }

    /**
     * Se rerodenan de forma que se pueda hacer gráficamente 
     *
     * @param  int  $id
     * @return int
     */
    public function reorder(Archive $archive, $position)
    {
        $user=Auth::user();
        $avatars = $user->archives()->where('type',1)->get();
        $num_avatars = count( $avatars );

        //Si la posicion a la que se quiere mover es la misma, no hace nada
        if ($archive->position == $position) { return 0; }

        //Si la posicion a la que se quiere mover es mayor que las fotos existentes o menor que 0, no hace nada.
        if ( $position > $num_avatars || $position < 0) { return 0; }

        //Si la posicion a la que se quiere mover es  mayor que la posición actual (mueve a derecha)
        if ($position > $archive->position){

            foreach ($avatar as $avatar) {
                
                if( $avatar->position > $archive->position && $avatar->position <= $position) {

                    $avatar->position++;
                    $avatar->save();
                }
            }

            $archive->position = $position;
            $archive->save();

            return 1;


        }

        //Si la posicion a la que se quiere mover es  mayor que la posición actual (mueve a izquierda)
        if ($position < $archive->position)

            foreach ($avatar as $avatar) {
                
                if( $avatar->position >= $position && $avatar->position < $archive->position) {

                    $avatar->position--;
                    $avatar->save();
                }
            }

            $archive->position = $position;
            $archive->save();


        return 1;
    }


}
