<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\StringAttributeToUppercase;

class ActivoTipo extends Model
{
    /**
     * Implementar SoftDelete, StringAttributeToUppercase
     * 
     */
    use SoftDeletes;
    use StringAttributeToUppercase;

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
	public function activos()
    {
    	return $this->hasMany(Activo::class, 'tipo_id');
    }

    public function grupos()
    {
        return $this->belongsToMany('App\Grupo', 'activo_tipos_grupos', 'tipo_id', 'grupo_id')
            ->withTimestamps();
    }
}
