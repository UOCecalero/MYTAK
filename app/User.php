<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
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
        'password', 'remember_token',
    ];

    /*** Añadido a partir de aqui (copiado de Perfil Eloquent Model)  *****/

    public function empresa()
    {
        return $this->hasOne(Empresa::class,'creator');
    }

    public function eventos()
    {
        return $this->belongsToMany(Evento::class);
    } 


   /***** ESTA FUNCIÓN SE DEBE LLAMAR COMO matches() CON PARÉNTESIS *****/
    public function matches()
     {
        $id_ref = $this->id;

        $res = DB::table('matches as m')->join( 'matches', 'm.usuario2_id', '=', 'matches.usuario1_id')
                ->where('m.usuario1_id', $id_ref)
                ->where('matches.usuario2_id', $id_ref)
                ->join('perfils', 'perfils.id', '=', 'matches.usuario1_id')
                ->select('perfils.*')
                ->get();

     //Esta función llama a la tabla matches y busca las tuplas que tienen como valor al usuario del que queremos saber sus matches. Una vez lo tiene hace un join donde la columna usuario1 = usuario2 de forma que sabemos a su vez si alguno de los matches del usuario a elegido a correspondido al usuario. Cuando sabe si hay algun usuario hace un join con la tabla perfiles para extraer sus datos.
        


        return collect($res);

     }

    /**
    public function matches()
    {

        return $this->hasMany(Match::class, 'usuario1_id');
    }

    **/

    public function bloqueados()
    {
        $this->morphMany(Bloqueado::class, 'bloqueador');
    }
}
