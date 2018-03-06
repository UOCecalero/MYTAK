<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'tickets', 'created_at', 'updated_at', 'destacado_ini', 'destacado_fin'
    ];
    

    public function empresa()
    {
    	return $this->belongsTo(Empresa::class,'creator');
    }

    
    public function prices()
    {
        return $this->hasMany(Price::class, 'evento_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'evento_id');
    }

    //Este campo se podría referir a los usuarios que tienen el evento como favorito
    //Los que han comprado el evento se extraen mediante ticket
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
