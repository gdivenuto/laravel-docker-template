<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Digesto extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'pivot'
    ];

    /**
     * Los descriptores que pertenecen a un digesto.
     * @return [type] [description]
     */
    public function descriptores()
    {
        return $this->belongsToMany(Descriptor::class)
            ->withPivot('condicion')
            ->withTimestamps();
    }

    /**
     * Los descriptores que pertenecen a un digesto, y son obligatorios.
     * @return [type] [description]
     */
    public function descriptores_and()
    {
        return $this->belongsToMany(Descriptor::class)
            ->where('condicion', '=', 'and')
            ->withPivot('condicion')
            ->withTimestamps();
    }

    /**
     * Los descriptores que pertenecen a un digesto, y son opcionales.
     * @return [type] [description]
     */
    public function descriptores_or()
    {
        return $this->belongsToMany(Descriptor::class)
            ->where('condicion', '=', 'or')
            ->withPivot('condicion')
            ->withTimestamps();
    }
}
