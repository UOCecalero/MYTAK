<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

	public function emisor()
    {
        return $this->hasOne(User::class,'emisor');
    }

    public function receptor()
    {
        return $this->hasOne(User::class,'receptor');
    }
    
}
