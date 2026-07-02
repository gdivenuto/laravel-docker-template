<?php
namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FakeAttrib;
use OwenIt\Auditing\Contracts\Auditable;

class Descriptor extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'descriptores';

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
    protected $touches = ['normas'];

    /**
     * Inter model relationship
     * 
     */
    public function bases() {
        return $this->hasMany(DescriptorBase::class, 'descriptor_id', 'id');
    }

    public function normas()
    {
    	return $this->belongsToMany(Norma::class, 'descriptor_norma', 'descriptor_id', 'norma_id')->withTimestamps();
    }

    /**
     * Se obtienen los descriptores con el número de ocurrencias de normas respectivas
     * @return [type] [description]
     */
    public static function getDescriptorsWithOccurrences($search, $base)
    {
        return self::select('descriptores.id', 'descriptores.tag', DB::raw('COUNT(descriptor_norma.norma_id) AS ocurrencias'))
            ->join('descriptor_bases', 'descriptores.id', '=', 'descriptor_bases.descriptor_id')
                ->where('descriptor_bases.base', '=', $base)
            ->join('descriptor_norma', 'descriptores.id', '=', 'descriptor_norma.descriptor_id')
                ->where('descriptores.tag', 'LIKE', "%$search%")
            ->groupBy('descriptores.id', 'descriptores.tag')
            ->get();
    }
}
