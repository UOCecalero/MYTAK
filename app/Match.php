<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
	
/**
	public function usuarios(){

		return $this->belongsTo(Usuario::class, 'usuario2_id');
	}
**/

public function evento(){

    	return $this->belongsTo(Evento::class, 'evento_id');

    }

}
