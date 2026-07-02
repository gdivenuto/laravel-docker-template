<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\StringAttributeToUppercase;

class Responsable extends Model
{
    /**
     * Implementar StringAttributeToUppercase
     * 
     */
    use StringAttributeToUppercase;

    /**
     * Custom Primary Key
     */
    protected $primaryKey = 'legajo';

    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'legajo';
    }

    /**
     * Inter model relationship
     * 
     */
	public function area()
    {
    	return $this->belongsTo(Area::class, 'cod_area', 'cod_area');
    }

    public function activos()
    {
    	return $this->hasMany(Activo::class, 'legajo', 'legajo');
    }
    
    /**
     * Accessors to get the calculated attributes bonuses.
     */
    public function getNombreCompletoAttribute()
    {
    	return "{$this->apellido}, {$this->nombre}";
    }
}
