<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    //

    public function empresa()
    {
    	return $this->belongsTo(Empresa::class,'creator');
    }

    public function perfiles()
    {
    	return $this->belongsToMany(Perfil::class);
    }

    public function matches()
    {
    	return $this->hasMany(Match::class, 'evento_id');
    }

     public function bloqueados()
    {
        $this->morphMany(Bloqueado::class, 'bloqueador');
    }
}
