<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class, 'user_id');
    }

    public function price()
    {
    	return $this->belongsTo(Price::class, 'price_id');
    }
}
