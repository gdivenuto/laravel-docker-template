<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Helpers\FakeAttrib;
use OwenIt\Auditing\Contracts\Auditable;

class Relacion extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'relaciones';

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
     * Inter model relationship
     * 
     */
    public function norma() {
        return $this->belongsTo(Norma::class);
    }
    
    public function relNormaByActoNro() {
        return Norma::where('acto', $this->a)->where('nro', $this->n)->first();
    }

    /**
     * Accessors
     */
    public function getRelacionDescAttribute()
    {
        return FakeAttrib::relacionDesc($this->sentido, $this->tipo);
    }

    public function getActoDescAttribute()
    {
        return FakeAttrib::tipoActoDesc($this->a);
    }

    public function getUrlHtmlAttribute() {
        $norma = $this->relNormaByActoNro();
    	return ($norma) ? $norma->url_html : '';
    }

    public function getUrlPdfAttribute() {
        $norma = $this->relNormaByActoNro();
        return ($norma) ? $norma->url_pdf : '';
    }
}
