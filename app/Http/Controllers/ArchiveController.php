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

        if ( $num_avatars  < env('MAX_AVATARS') ){

            $path = request()->file('avatar')->store('avatars');

            foreach ($avatars as $item) {

                $item->position ++;
                $item->save();
            }

            $archive = new Archive;
            $archive->user_id = $user->id;
            $archive->path = $path;
            $archive->position = 1;
            $archive->type = 1;
            $archive->save();

            $user->photo = $path;
            $user->save();

            return 1;

        } else { 
            
            return 0; 
        }
    }
}
