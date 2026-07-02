<?php

namespace App\Http\Controllers;
use DB;

use App\Descriptor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class DescriptorController extends Controller
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
     * @param  \App\Descriptor  $descriptor
     * @return \Illuminate\Http\Response
     */
    public function show(Descriptor $descriptor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Descriptor  $descriptor
     * @return \Illuminate\Http\Response
     */
    public function edit(Descriptor $descriptor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Descriptor  $descriptor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Descriptor $descriptor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Descriptor  $descriptor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Descriptor $descriptor)
    {
        //
    }

    /**
     * [getAutocompleteDescriptorJson description]
     * @return [type] [description]
     */
    public function getAutocompleteDescriptorJson(Request $request) {
        $jsonData = [];

        // Validación de $normas_db
        $normas_db = ($request->has('normas_db')) ? $request->normas_db : 'todas';
        //if (! in_array($normas_db, ['digesto', 'normas', 'sesiones', 'decretos', 'todas'])) 
        //    return ['results' => []];

        // Busco resultados
        if ($request->has('q')) {
            $search = $request->q;
            
            $jsonData = DB::table('descriptores as D')
                ->select('D.id as id', 'D.tag as text');

            // if ($normas_db != 'todas')
            if (in_array($normas_db, ['normas', 'sesiones', 'decretos']))
                $jsonData = $jsonData->join('descriptor_bases as DBASES', 'D.id', '=', 'DBASES.descriptor_id')
                    ->where('DBASES.base', '=', $normas_db);
           
            $jsonData = $jsonData->where('D.tag', 'LIKE', "%$search%")->get();
        }

        return ['results' => $jsonData];
    }

    /**
     * [getDescriptorByIdJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getDescriptorByIdJson(Request $request) {
        // Validación de parametros -------------------------------------------
        $validator = Validator::make(
            $request->all(), 
            [
                'id_list' => 'required|array',
                'id_list.*' => 'integer'
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error en validación de parámetros: '.implode("\n",$validator->errors()->all()),
                'data' => null
            ]);
        }

        // Respuesta ----------------------------------------------------------
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => Descriptor::whereIn('id', $request->id_list)->get()->pluck('tag', 'id')->toArray()
        ]);
    }
}
