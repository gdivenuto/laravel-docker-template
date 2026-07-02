<?php

namespace App\Http\Controllers;

use App\Procesamiento;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\ProcesamientosSaveRequest;

//use Illuminate\Support\Facades\Log;

class BackendProcesamientosController extends Controller
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
        $procesamientos = $norma->procesamientos->map(function ($data) { 
            return [
                'id'=>$data->id, 
                'nombre'=>$data->nombre
            ]; 
        });
        return view('backend.procesamientos.edit', compact('norma', 'procesamientos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProcesamientosSaveRequest $request, Norma $norma)
    {
        $data = [];

        if ($request->has('procesamientos'))
            foreach ($request->procesamientos as $param) {
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
            $item = $norma->procesamientos()->find($d['id']);
            $item->nombre = $d['nombre'];
            $norma->procesamientos()->save($item);
        }

        // Delete de los faltantes
        $norma->procesamientos()->whereNotIn('id', $data_ids)->delete();

        // Insert de los nuevos (creo, mapeo, guardo)
        foreach ($data_sin_id as $d) {
            $item = new Procesamiento();
            $item->nombre = $d['nombre'];
            $norma->procesamientos()->save($item);
        }
        
        return redirect()
            ->route('backend.procesamientos.edit', ['norma' => $norma])
            ->with('save_status', 'Procesamientos guardados con éxito.');
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
