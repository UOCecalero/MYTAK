<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'ranking', 'created_at', 'destacado_ini', 'destacado_fin',
    ];



    /******************* Añadido a partir de aqui (copiado de Perfil Eloquent Model)  ******************/

    public function empresa()
    {
        return $this->hasOne(Empresa::class,'creator');
    }

    public function eventos()
    {
        return $this->belongsToMany(Evento::class, 'evento_user');
    } 


   /***** ESTA FUNCIÓN SE DEBE LLAMAR COMO matches() CON PARÉNTESIS *****/
    public function matches()
     {
        $id_ref = $this->id;

        $res = DB::table('matches as m')->join( 'matches', 'm.usuario2_id', '=', 'matches.usuario1_id')
                ->where('m.usuario1_id', $id_ref)
                ->where('matches.usuario2_id', $id_ref)
                ->join('users', 'users.id', '=', 'matches.usuario1_id')
                ->select('users.*')
                ->get();

     //Esta función llama a la tabla matches y busca las tuplas que tienen como valor al usuario del que queremos saber sus matches. Una vez lo tiene hace un join donde la columna usuario1 = usuario2 de forma que sabemos a su vez si alguno de los matches del usuario a elegido a correspondido al usuario. Cuando sabe si hay algun usuario hace un join con la tabla perfiles para extraer sus datos.

        return $res;

     }


    public function bloqueados()
    {
        return $this->morphToMany(User::class, 'bloqueador');
    }

       public function users()
    {
        return $this->morphedByMany(User::class, 'bloqueador');
    }

    public function empresas()
    {
        return $this->morphedByMany(Empresa::class, 'bloqueador');
    }
}
