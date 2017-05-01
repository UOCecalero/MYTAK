<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
   
    public function perfil()
    {
    	return $this->belongsTo(Perfil::class,'creator');
    }




    public function eventos()
    {
    	return $this->hasMany(Evento::class,'creator');
    }
}
