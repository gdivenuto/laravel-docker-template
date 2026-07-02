<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Traits\StringAttributeToUppercase;

class Area extends Model
{
    /**
     * Implementar StringAttributeToUppercase
     * 
     */
    use StringAttributeToUppercase;

    /**
     * Custom Primary Key
     */
    protected $primaryKey = 'cod_area';

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
	public function responsables()
    {
    	return $this->hasMany(Responsable::class, 'cod_area', 'cod_area');
    }
}
