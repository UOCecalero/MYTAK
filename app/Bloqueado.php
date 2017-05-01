<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bloqueado extends Model
{
      public function bloqueador()
    {
    	return $this->morphTo();
    }
}
