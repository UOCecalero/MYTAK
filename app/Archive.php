<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    public function user()
    {
    	return $this->belongsToMany(User::class,'user_id');
    }
}
