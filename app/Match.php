<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
	
/**
	public function perfiles(){

		return $this->belongsTo(Perfil::class, 'usuario2_id');
	}
**/

public function evento(){

    	return $this->belongsTo(Evento::class, 'evento_id');

    }

}
