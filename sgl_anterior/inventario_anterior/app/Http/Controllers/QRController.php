<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Activo;

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
     * Genera un código QR para la obtención de info de un Activo.
     * @param  Activo $activo [description]
     * @return [type]         [description]
     */
    public function generateActivoQR(Activo $activo) 
    {
    	$content = QrCode::format('png')
    		->size(200)
    		->margin(1)
    		->generate(route('activos.show', [ 'id' => $activo ]));

		return response($content)
			->header('Content-Type','image/png')
			->header('Pragma','public')
			->header('Content-Disposition', sprintf('inline; filename="activo_%s.png"', $activo->id))
			->header('Cache-Control','max-age=60, must-revalidate');
    }
}
