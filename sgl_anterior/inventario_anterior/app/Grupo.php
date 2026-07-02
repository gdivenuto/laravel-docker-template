<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Inter model relationship
     * 
     */
    public function activo_tipos()
    {
    	return $this->belongsToMany('App\ActivoTipo', 'activo_tipos_grupos', 'grupo_id', 'tipo_id')
    		->withTimestamps();
    }
}
