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

    //prices()
    public function tickets()
    {
        return $this->hasMany(Price::class, 'evento_id');
    }

    public function users()
    {
    	return $this->belongsToMany(User::class, 'evento_user');
    }

    public function matches()
    {
    	return $this->hasMany(Match::class, 'evento_id');
    }

     public function bloqueados()
    {
        $this->morphToMany(Bloqueado::class, 'bloqueador');
    }
}
