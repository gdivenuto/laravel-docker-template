<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\FakeAttrib;
use OwenIt\Auditing\Contracts\Auditable;

//use App\Traits\FullTextSearch;

class Norma extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    //use FullTextSearch;

    /**
     * The attributes that should protected.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     * Will be added at json serialization.
     *
     * @var array
     */
    protected $appends = [
        'acto_desc',
        'url_html',
        'url_pdf',
        'url_actualizado_html',
        'url_actualizado_pdf',
        'base_tag'
    ];

    /**
     * The columns of the FULL TEXT index.
     * 
     * @var [type]
     */
    protected $searchable = [
        'origen',
        'nro_hcd',
        'exped',
        'bloque',
        'hcd_exped',
        'dec_promulga',
        'boletin_nro',
        'boletin_pag',
        'abrogacion_a',
        'abrogacion_n',
        'contenido',
        'nro_tema',
        'recopila',
        'sin_nro',
        'ingresa',
        'procesa',
        'aprobado',
        'acto_nro'
    ];

    /**
     * Accessors de campos existentes
     */
    public function getNroHcdAttribute($value)
    {
        return strToUpper($value);
    }

    public function getRecopilaAttribute($value)
    {
        return strToUpper($value);
    }    

    public function getAlcanceAttribute($value)
    {
        return strToUpper($value);
    }

    public function getCaracterAttribute($value)
    {
        return strToUpper($value);
    }

    public function getAprobadoAttribute($value)
    {
        return strToUpper($value);
    }    

    /**
     * Accessors de campos ficticios
     */
    public function getActoDescAttribute()
    {
        // Agreagado a '$appends' para que se serialize en JSON.
        return FakeAttrib::tipoActoDesc($this->acto);
    }

    public function getAbrogacionADescAttribute()
    {
        return FakeAttrib::tipoActoDesc($this->abrogacion_a);
    }

    public function getAlcanceDescAttribute()
    {
        return FakeAttrib::alcanceDesc($this->alcance);
    }

    public function getCaracterDescAttribute()
    {
        return FakeAttrib::caracterDesc($this->caracter);
    }

    public function getRecopilaDescAttribute()
    {
        return FakeAttrib::snDesc($this->recopila);
    }

    public function getAprobadoDescAttribute()
    {
        return FakeAttrib::aprobadoDesc($this->aprobado);
    }

    public function getUrlHtmlAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->acto, $this->nro, $this->nro_hcd, 'html');
    }

    public function getUrlPdfAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->acto, $this->nro, $this->nro_hcd, 'pdf');
    }

    public function getUrlAbrogacionHtmlAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->abrogacion_a, $this->abrogacion_n, 'no-existe', 'html');
    }

    public function getUrlAbrogacionPdfAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->abrogacion_a, $this->abrogacion_n, 'no-existe', 'pdf');
    }

    public function getUrlActualizadoHtmlAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->acto, $this->nro, $this->nro_hcd, 'html', true);
    }

    public function getUrlActualizadoPdfAttribute() {
        return FakeAttrib::urlDoc($this->base, $this->acto, $this->nro, $this->nro_hcd, 'pdf', true);
    }

    public function getBaseTagAttribute() {
        return FakeAttrib::baseTag($this->base, $this->dec_promulga, $this->recopila);
    }

    /**
     * Inter model relationship
     * -----------------------------------------
     */
    
    // 2023-06-12 no se utiliza esta relación, 
    // se deja de utilizar la tabla 'hcd_expedientes', 
    // se agregó el campo hcd_exped en 'normas',
    // porque es una relación de 1 a 1
    // 
    // public function hcd_expedientes()
    // {
    // 	// hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
    // 	return $this->hasMany(HcdExpediente::class);
    // }

    public function actas()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
    	return $this->hasMany(Acta::class);
    }

    public function descriptores()
    {
    	return $this->belongsToMany(Descriptor::class, 'descriptor_norma', 'norma_id', 'descriptor_id')->withTimestamps();
    }

    public function relaciones()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
    	return $this->hasMany(Relacion::class);
    }

    // 2023-07-03 no se utiliza esta relación, 
    // se deja de utilizar la tabla 'procesamientos', 
    // se agregó el campo 'procesa' en 'normas',
    // porque es una relación de 1 a 1
    // 
    // public function procesamientos()
    // {
    // 	// hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
    // 	return $this->hasMany(Procesamiento::class);
    // }

    public function abstenciones()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
    	return $this->hasMany(Abstencion::class);
    }

    public function observaciones()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = norma_id, localKey = id)
        return $this->hasMany(Observacion::class);
    }

    /**
     * Comportamientos adicionales
     * 
     */
    public function clonar()
    {
        $clon = $this->replicate();
        $clon->save();

        // Relaciones 1:N
        // ----------------------------------------------------
        
        // 2023-06-12 no se utiliza esta relación, 
        // se deja de utilizar la tabla 'hcd_expedientes', 
        // se agregó el campo hcd_exped en 'normas',
        // porque es una relación de 1 a 1
        // -----------------------------------------
        //foreach ($this->hcd_expedientes as $h)
        //    $clon->hcd_expedientes()->create($h->replicate()->toArray());

        foreach ($this->actas as $a)
            $clon->actas()->create($a->replicate()->toArray());

        foreach ($this->relaciones as $r)
            $clon->relaciones()->create($r->replicate()->toArray());

        // 2023-07-03 no se utiliza esta relación, 
        // se deja de utilizar la tabla 'procesamientos', 
        // se agregó el campo 'procesa' en 'normas',
        // porque es una relación de 1 a 1
        // -----------------------------------------
        // foreach ($this->procesamientos as $p)
        //     $clon->procesamientos()->create($p->replicate()->toArray());

        foreach ($this->abstenciones as $a)
            $clon->abstenciones()->create($a->replicate()->toArray());

        foreach ($this->observaciones as $o)
            $clon->observaciones()->create($o->replicate()->toArray());

        // Los descriptores tienen un tratamiento especial,
        // por ser una relacion N:M
        $lista_descriptores_id = $this->descriptores()->pluck('id')->toArray();
        $clon->descriptores()->sync($lista_descriptores_id);

        return $clon;
    }

}
