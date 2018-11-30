<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

     'created_at', 'udpdated_at', 'user_id'
    
    ];

    public function user()
    {
    	return $this->belongsToMany(User::class,'user_id');
    }
}
