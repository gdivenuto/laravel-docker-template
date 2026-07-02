<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\StringAttributeToUppercase;

use App\Helpers\FakeAttrib;

class Activo extends Model
{
    /**
     * Implementar SoftDelete, StringAttributeToUppercase
     * 
     */
    use SoftDeletes;
    use StringAttributeToUppercase;

    /**
     * Exclude attributes from StringAttributeToUppercase
     * @var array
     */
    protected $exclude_uppercase = [ 
        'sistema_operativo_serie',
        'ethernet_dns',
        'wireless_dns'
    ];

    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Conjunto de accessors que seran agregados al array form de la clase.
     * Esto se utiliza para que sean serializados al convertirlos a json.
     *
     * @var array
     */
    protected $appends = [
        'tipo_origen_desc',
        'titularidad_desc',
        'estado_desc',
        'condicion_uso_desc',
        'desc_dinamica'
    ];

    /**
     * Accessors para los campos con tabla "fija".
     */
    public function getTipoOrigenDescAttribute()
    {
        return FakeAttrib::tipoOrigenDesc($this->tipo_origen);
    }

    public function getTitularidadDescAttribute()
    {
        return FakeAttrib::titularidadDesc($this->titularidad);
    }

    public function getEstadoDescAttribute()
    {
        return FakeAttrib::estadoDesc($this->estado);
    }

    public function getCondicionUsoDescAttribute()
    {
        return FakeAttrib::condicionUsoDesc($this->condicion_uso);
    }

    public function getHabilitadoDescAttribute()
    {
        return FakeAttrib::habilitadoDesc($this->habilitado);
    }

    public function getDescDinamicaAttribute()
    {
        return FakeAttrib::descDinamica($this, $this->activo_tipo->atrib_desc_dinamica);
    }

    /**
     * Inter model relationship
     * 
     */
	public function activo_tipo()
    {
    	return $this->belongsTo(ActivoTipo::class, 'tipo_id');
    }

	public function responsable()
    {
    	return $this->belongsTo(Responsable::class, 'legajo', 'legajo');
    }

	public function user()
    {
    	return $this->belongsTo(Users::class);
    }
}
