<?php

namespace App\Http\Controllers;

use App\Intendencia;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\IntendenciaSaveRequest;

class BackendIntendenciasController extends Controller
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
        return view('backend.intendencias.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $intendencia = new Intendencia();
        return view('backend.intendencias.edit', compact('intendencia'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(IntendenciaSaveRequest $request)
    {
        // Las validaciones estan en /app/Http/Requests/IntendenciaSaveRequest
        $intendencia = Intendencia::create([
            'intendente' => $request->intendente ?? '',
            'nro' => $request->nro ?? '',
            'fec_desde' => $request->fec_desde,
            'fec_hasta' => $request->fec_hasta
        ]);

        return redirect()
            ->route('backend.intendencias.edit', ['intendencia' => $intendencia])
            ->with('save_status', sprintf('Intendencia guardada con éxito (ID %d).', $intendencia->id));
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
    public function edit(Intendencia $intendencia)
    {
        return view('backend.intendencias.edit', compact('intendencia'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(IntendenciaSaveRequest $request, Intendencia $intendencia)
    {
        // Las validaciones estan en /app/Http/Requests/IntendenciaSaveRequest
        $intendencia->fill([
            'intendente' => $request->intendente ?? '',
            'nro' => $request->nro ?? '',
            'fec_desde' => $request->fec_desde,
            'fec_hasta' => $request->fec_hasta
        ]);

        $intendencia->save();

        return redirect()
            ->route('backend.intendencias.edit', ['intendencia' => $intendencia])
            ->with('save_status', sprintf('Intendencia guardada con éxito (ID %d).', $intendencia->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Intendencia $intendencia)
    {
        $intendencia->delete();
        
        return redirect()
            ->route('backend.intendencias.index')
            ->with('save_status', 'Intendencia eliminada con éxito.');
    }

    /**
     * Returns statistic data
     * 
     * @return [type] [description]
     */
    public function dtGetIntendenciasJson() 
    {
        $data = Intendencia::query();

        return datatables()->eloquent($data)->toJson();
    }    

}
