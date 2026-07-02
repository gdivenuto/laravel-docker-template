<?php

namespace App\Http\Controllers;

use App\Relacion;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\RelacionesSaveRequest;

//use Illuminate\Support\Facades\Log;

class BackendRelacionesController extends Controller
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Norma $norma)
    {
        $relaciones = $norma->relaciones->map(function ($data) { 
            return [
                'id' => $data->id, 
                'sentido' => $data->sentido,
                'tipo' => $data->tipo,
                'a' => $data->a,
                'n' => $data->n,
                'p' => $data->p
            ]; 
        });
        $norma_tipo_acto = FakeAttrib::tipoActoList();
        return view('backend.relaciones.edit', compact('norma', 'relaciones', 'norma_tipo_acto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RelacionesSaveRequest $request, Norma $norma)
    {
        $data = [];

        if ($request->has('relaciones'))
            foreach ($request->relaciones as $param) {
                $data[] = [
                    'id' => $param['id'], 
                    'sentido' => $param['sentido'],
                    'tipo' => $param['tipo'],
                    'a' => $param['a'],
                    'n' => $param['n'],
                    'p' => $param['p']
                ];
            }

        // Separo los datos entre los que tienen o no ID
        $data_sin_id = collect($data)->whereNull('id');
        $data_con_id = collect($data)->whereNotNull('id');
        $data_ids = $data_con_id->pluck('id');

        // Update de los que tienen ID (busco, mapeo, actualizo)
        foreach ($data_con_id as $d) {
            $item = $norma->relaciones()->find($d['id']);
            $item->sentido = $d['sentido'];
            $item->tipo = $d['tipo'];
            $item->a = $d['a'];
            $item->n = $d['n'];
            $item->p = $d['p'];
            $norma->relaciones()->save($item);
        }

        // Delete de los faltantes
        $norma->relaciones()->whereNotIn('id', $data_ids)->delete();

        // Insert de los nuevos (creo, mapeo, guardo)
        foreach ($data_sin_id as $d) {
            $item = new Relacion();
            $item->sentido = $d['sentido'];
            $item->tipo = $d['tipo'];
            $item->a = $d['a'];
            $item->n = $d['n'];
            $item->p = $d['p'];
            $norma->relaciones()->save($item);
        }
        
        return redirect()
            ->route('backend.relaciones.edit', ['norma' => $norma])
            ->with('save_status', 'Relaciones guardadas con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
