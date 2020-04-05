<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];
    public function tickets()
    {
    	return $this->hasMany(Ticket::class, 'price_id');
    }

    public function evento()
    {
    	return $this->belongsTo(Evento::class, 'evento_id');
    }
}
