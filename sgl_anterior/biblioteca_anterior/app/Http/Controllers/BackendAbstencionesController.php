<?php

namespace App\Http\Controllers;

use App\Abstencion;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\AbstencionesSaveRequest;

//use Illuminate\Support\Facades\Log;

class BackendAbstencionesController extends Controller
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
        $abstenciones = $norma->abstenciones->map(function ($data) { 
            return [
                'id'=>$data->id, 
                'nombre'=>$data->nombre
            ]; 
        });
        return view('backend.abstenciones.edit', compact('norma', 'abstenciones'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AbstencionesSaveRequest $request, Norma $norma)
    {
        $data = [];

        if ($request->has('abstenciones'))
            foreach ($request->abstenciones as $param) {
                $data[] = [
                    'id' => $param['id'],
                    'nombre' => $param['nombre'],
                ];
            }

        // Separo los datos entre los que tienen o no ID
        $data_sin_id = collect($data)->whereNull('id');
        $data_con_id = collect($data)->whereNotNull('id');
        $data_ids = $data_con_id->pluck('id');

        // Update de los que tienen ID (busco, mapeo, actualizo)
        foreach ($data_con_id as $d) {
            $item = $norma->abstenciones()->find($d['id']);
            $item->nombre = $d['nombre'];
            $norma->abstenciones()->save($item);
        }

        // Delete de los faltantes
        $norma->abstenciones()->whereNotIn('id', $data_ids)->delete();

        // Insert de los nuevos (creo, mapeo, guardo)
        foreach ($data_sin_id as $d) {
            $item = new Abstencion();
            $item->nombre = $d['nombre'];
            $norma->abstenciones()->save($item);
        }
        
        return redirect()
            ->route('backend.abstenciones.edit', ['norma' => $norma])
            ->with('save_status', 'Abstenciones guardadas con éxito.');
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
