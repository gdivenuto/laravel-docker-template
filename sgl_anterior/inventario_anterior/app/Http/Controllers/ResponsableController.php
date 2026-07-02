<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Responsable;

class ResponsableController extends Controller
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
     * [getAutocompleteResponsableJson description]
     * @return [type] [description]
     */
    public function getAutocompleteResponsableJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('responsables')
            ->join('areas', 'responsables.cod_area', '=', 'areas.cod_area')
            ->select('responsables.legajo', 'responsables.apellido', 'responsables.nombre', 'areas.nombre as area_nombre');

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('responsables.legajo','LIKE', "%$search%")
                ->orWhere('responsables.apellido','LIKE', "%$search%")
                ->orWhere('responsables.nombre','LIKE', "%$search%")
                ->orWhere('areas.nombre','LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->orderBy('responsables.apellido', 'asc')->orderBy('responsables.nombre', 'asc')->get();

        return response()->json($data);
/*
        $data = [];

        if ($request->has('q')) {
            $search = $request->q;
            $data = DB::table('responsables')
                ->join('areas', 'responsables.cod_area', '=', 'areas.cod_area')
                ->select('responsables.legajo', 'responsables.apellido', 'responsables.nombre', 'areas.nombre as area_nombre')
                ->where('responsables.legajo','LIKE', "%$search%")
                ->orWhere('responsables.apellido','LIKE', "%$search%")
                ->orWhere('responsables.nombre','LIKE', "%$search%")
                ->orWhere('areas.nombre','LIKE', "%$search%")
                ->get();
        }

        return response()->json($data);
*/
    }

    /**
     * [getAutocompleteResponsableFiltroJson description]
     * @return [type] [description]
     */
    public function getAutocompleteResponsableFiltroJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('responsables')
            ->join('areas', 'responsables.cod_area', '=', 'areas.cod_area')
            ->join('activos', 'responsables.legajo', '=', 'activos.legajo')
            ->select('responsables.legajo', 'responsables.apellido', 'responsables.nombre', 'areas.nombre as area_nombre')
            ->whereNull('activos.deleted_at');

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('responsables.legajo','LIKE', "%$search%")
                ->orWhere('responsables.apellido','LIKE', "%$search%")
                ->orWhere('responsables.nombre','LIKE', "%$search%")
                ->orWhere('areas.nombre','LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->orderBy('responsables.apellido', 'asc')->orderBy('responsables.nombre', 'asc')
            ->distinct()
            ->get();

        return response()->json($data);
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
     * @param  \App\Responsable  $responsable
     * @return \Illuminate\Http\Response
     */
    public function show(Responsable $responsable)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Responsable  $responsable
     * @return \Illuminate\Http\Response
     */
    public function edit(Responsable $responsable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Responsable  $responsable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Responsable $responsable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Responsable  $responsable
     * @return \Illuminate\Http\Response
     */
    public function destroy(Responsable $responsable)
    {
        //
    }
}
