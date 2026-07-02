<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Acta extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    const ACTAS = [
        'amc' => 'Asamblea de Cjales. y Mayores Contribuyentes',
        'esp' => 'Especial',
        'ext' => 'Extraordinaria',
        'ord' => 'Ordinaria',
        'ordpro' => 'Ordinaria de Prórroga',
        'prep' => 'Preparatoria'
    ];

    public function getTipoNombreAttribute()
    {
        return self::ACTAS[$this->acta_t] ?? '-';
    }

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
