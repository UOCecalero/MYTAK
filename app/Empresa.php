<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{

    /**
     * The attributes that are mass assignable. PASSWORD?????
     *
     * @var array
     */
    protected $fillable = [
        
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pwd'
    ];
   
    public function user()
    {
    	return $this->belongsTo(User::class,'creator');
    }

    public function eventos()
    {
    	return $this->hasMany(Evento::class,'creator');
    }

     public function bloqueados()
    {
        //return $this->morphToMany(Bloqueado::class, 'bloqueador');
        return $this->morphToMany(User::class, 'bloqueador');
    }
}
