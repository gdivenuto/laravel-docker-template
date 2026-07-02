<?php

namespace App\Http\Controllers;

use App\Acta;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\ActaSaveRequest;

//use Illuminate\Support\Facades\Log;

class BackendActasController extends Controller
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
        $actas = $norma->actas->map(function ($data) { 
            return [
                'id'=>$data->id, 
                'acta_n'=>$data->acta_n, 
                'acta_r'=>$data->acta_r, 
                'acta_t'=>$data->acta_t
            ]; 
        });
        return view('backend.actas.edit', compact('norma', 'actas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ActaSaveRequest $request, Norma $norma)
    {
        $data = [];

        if ($request->has('actas'))
            foreach ($request->actas as $param) {
                $data[] = [
                    'id' => $param['id'],
                    'acta_n' => $param['acta_n'],
                    'acta_r' => $param['acta_r'],
                    'acta_t' => $param['acta_t'],
                ];
            }

        // Separo los datos entre los que tienen o no ID
        $data_sin_id = collect($data)->whereNull('id');
        $data_con_id = collect($data)->whereNotNull('id');
        $data_ids = $data_con_id->pluck('id');

        // Update de los que tienen ID (busco, mapeo, actualizo)
        foreach ($data_con_id as $d) {
            $item = $norma->actas()->find($d['id']);
            $item->acta_n = $d['acta_n'];
            $item->acta_r = $d['acta_r'];
            $item->acta_t = $d['acta_t'];
            $norma->actas()->save($item);
        }

        // Delete de los faltantes
        $norma->actas()->whereNotIn('id', $data_ids)->delete();

        // Insert de los nuevos (creo, mapeo, guardo)
        foreach ($data_sin_id as $d) {
            $item = new Acta();
            $item->acta_n = $d['acta_n'];
            $item->acta_r = $d['acta_r'];
            $item->acta_t = $d['acta_t'];
            $norma->actas()->save($item);
        }
        
        return redirect()
            ->route('backend.actas.edit', ['norma' => $norma])
            ->with('save_status', 'Actas guardadas con éxito.');
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
