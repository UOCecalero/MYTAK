<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    public function tickets()
    {
    	return $this->hasMany(Ticket::class, 'price_id');
    }

    public function price()
    {
    	return $this->belongsTo(Evento::class, 'evento_id');
    }
}
