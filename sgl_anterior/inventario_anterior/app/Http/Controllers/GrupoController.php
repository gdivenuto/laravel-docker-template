<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Grupo;
use App\ActivoTipo;

class GrupoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

	/**
	 * [jsonGetTiposById description]
	 * @param  [type] $grupo_id [description]
	 * @return [type]           [description]
	 */
	public function jsonGetTiposById($grupo_id) 
	{
		if ($grupo_id == 'all') {
			$activo_tipos = ActivoTipo::all()->map->only(['id','nombre'])->toArray();
		} else {
			try {
				$grupo = Grupo::findOrFail($grupo_id);
			} catch (Exception $e) {
				return response()->json([
					'status' => 'ERROR',
					'message' => sprintf('Error al obtener tipos asociados al grupo: %s', $e->getMessage()),
					'data' => null
				]);
			}
			$activo_tipos = $grupo->activo_tipos()->get()->map->only(['id','nombre'])->toArray();
		}

		return response()->json([
			'status' => 'OK',
			'message' => 'Tipos asociados a Grupo obtenidos con éxito.',
			'data' => $activo_tipos
		]);
	}
}
