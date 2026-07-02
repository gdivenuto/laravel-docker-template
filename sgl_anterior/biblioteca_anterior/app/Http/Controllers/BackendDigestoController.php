<?php

namespace App\Http\Controllers;

use App\Digesto;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\DigestoSaveRequest;

class BackendDigestoController extends Controller
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
        return view('backend.digestos.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $digesto = new Digesto();
        return view('backend.digestos.edit', compact('digesto'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DigestoSaveRequest $request)
    {
        // Las validaciones estan en /app/Http/Requests/DigestoSaveRequest
        $digesto = Digesto::create([
            'nombre' => strtolower($request->nombre),
            'publicado' => $request->publicado == 'S',
            'descripcion' => $request->descripcion ?? '',
            'filtro' => base64_decode($request->filtro, true)
        ]);

        return redirect()
            ->route('backend.digestos.edit', ['digesto' => $digesto])
            ->with('save_status', sprintf('Digesto guardado con éxito (ID %d).', $digesto->id));
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
    public function edit(Digesto $digesto)
    {
        return view('backend.digestos.edit', compact('digesto'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DigestoSaveRequest $request, Digesto $digesto)
    {
        // Las validaciones estan en /app/Http/Requests/DigestoSaveRequest
        $digesto->fill([
            'nombre' => strtolower($request->nombre),
            'publicado' => $request->publicado == 'S',
            'descripcion' => $request->descripcion ?? '',
            'filtro' => base64_decode($request->filtro, true)
        ]);

        $digesto->save();

        return redirect()
            ->route('backend.digestos.edit', ['digesto' => $digesto])
            ->with('save_status', sprintf('Digesto guardado con éxito (ID %d).', $digesto->id));
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

    /**
     * Returns statistic data
     * 
     * @return [type] [description]
     */
    public function dtGetDigestosJson() 
    {
        $data = Digesto::query();

        return datatables()->eloquent($data)->toJson();
    }    
}
