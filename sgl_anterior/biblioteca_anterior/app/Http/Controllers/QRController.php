<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Norma;
use App\Digesto;
use App\Descriptor;

class QRController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Genera un código QR con un URL de consulta a una norma.
     * @param  [type] $normas_db [description]
     * @param  Norma  $norma     [description]
     * @return [type]            [description]
     */
    public function generateNormaUrlQR($normas_db, Norma $norma) 
    {
        $content = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate(route('normas.show', [ 'normas_db' => $normas_db, 'norma' => $norma ]));

        return response($content)
            ->header('Content-Type','image/png')
            ->header('Pragma','public')
            ->header('Content-Disposition', sprintf('inline; filename="norma_%s.png"', $norma->id))
            ->header('Cache-Control','max-age=60, must-revalidate');
    }

    /**
     * [generateSearchSimpleTagQR description]
     * @param  string $normas_db        [description]
     * @param  string $descriptor_logic [description]
     * @param  string $tag_str          [description]
     * @return [type]                   [description]
     */
    public function generateSearchSimpleTagQR($normas_db = 'normas', $descriptor_logic = 'or', $tag_str = '')
    {
        // Limpio la lista de tags (deben existir para ser validos)
        $tag_list = explode('|', $tag_str);
        $default_descriptors = Descriptor::whereIn('tag', $tag_list)
            ->get()
            ->map(function ($d) {
                return $d->tag;
            })
            ->toArray();
        
        $digesto = Digesto::where('nombre', $normas_db)->first();

        $content = QrCode::format('png')
            ->size(200)
            ->margin(1)
            ->generate(
                route('normas.searchsimpletag', [ 
                    'normas_db' => $normas_db, 
                    'descriptor_logic' => $descriptor_logic, 
                    'tag_str' => implode('|', $default_descriptors)
                ])
            );

        return response($content)
            ->header('Content-Type','image/png')
            ->header('Pragma','public')
            ->header('Content-Disposition', 'inline; filename="searchsimpletagqr.png"')
            ->header('Cache-Control','max-age=60, must-revalidate');
    }

}
