<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Procesamiento extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];
    
    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['norma'];

    /**
     * Devuelve la norma a la que pertenece la abstención.
     */
    public function norma()
    {
        return $this->belongsTo(Norma::class);
    }
}
