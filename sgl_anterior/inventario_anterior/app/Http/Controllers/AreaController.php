<?php

namespace App\Http\Controllers;

use App\Area;
use Illuminate\Http\Request;
use DB;

class AreaController extends Controller
{
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
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function edit(Area $area)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Area $area)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Area  $area
     * @return \Illuminate\Http\Response
     */
    public function destroy(Area $area)
    {
        //
    }

    /**
     * [getAutocompleteAreaJson description]
     * @return [type] [description]
     */
    public function getAutocompleteAreaJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('areas')
            ->select('cod_area', 'nombre')
            ->whereNotNull('nombre')
            ->where('nombre', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('nombre', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        return response()->json($data);        
    }
}
