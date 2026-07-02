<?php

namespace App\Http\Controllers;

use App\HcdExpediente;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\HcdExpedienteSaveRequest;

//use Illuminate\Support\Facades\Log;

class BackendHcdExpedientesController extends Controller
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
        $hcd_expedientes_norma = $norma->hcd_expedientes->map(function ($data) { 
            return [
                'id'=>$data->id, 
                'hcd_exped'=>$data->hcd_exped
            ]; 
        });

        //dd($hcd_expedientes_norma[0]);

        $documentos = (isset($hcd_expedientes_norma[0])) 
            ? $this->getProyectos($hcd_expedientes_norma[0]['hcd_exped'])
            : null;
        
        //dd($documentos);

        //return view('backend.hcd_expedientes.edit', compact(['norma', 'hcd_expedientes_norma', 'documentos']));
        return view('backend.hcd_expedientes.edit')
            ->with('norma', $norma)
            ->with('hcd_expedientes_norma', $hcd_expedientes_norma)
            ->with('documentos', $documentos);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(HcdExpedienteSaveRequest $request, Norma $norma)
    {
        $data = [];

        if ($request->has('hcd_expedientes'))
            foreach ($request->hcd_expedientes as $param) {
                $data[] = [
                    'id' => $param['id'],
                    'hcd_exped' => $param['hcd_exped'],
                ];
            }

        // Separo los datos entre los que tienen o no ID
        $data_sin_id = collect($data)->whereNull('id');
        $data_con_id = collect($data)->whereNotNull('id');
        $data_ids = $data_con_id->pluck('id');

        // Update de los que tienen ID (busco, mapeo, actualizo)
        foreach ($data_con_id as $d) {
            $item = $norma->hcd_expedientes()->find($d['id']);
            $item->hcd_exped = $d['hcd_exped'];
            $norma->hcd_expedientes()->save($item);
        }

        // Delete de los faltantes
        $norma->hcd_expedientes()->whereNotIn('id', $data_ids)->delete();

        // Insert de los nuevos (creo, mapeo, guardo)
        foreach ($data_sin_id as $d) {
            $item = new HcdExpediente();
            $item->hcd_exped = $d['hcd_exped'];
            $norma->hcd_expedientes()->save($item);
        }
        
        return redirect()
            ->route('backend.hcdexpedientes.edit', ['norma' => $norma])
            ->with('save_status', 'Expedientes HCD guardados con éxito.');
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
