<?php

namespace App\Http\Controllers;

use App\Digesto;
use App\Descriptor;
use App\DescriptorBase;
use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Http\Requests\DescriptorNormasSaveRequest;

class BackendDescriptorNormasController extends Controller
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
    public function index(Norma $norma)
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
        return view('backend.descriptor_normas.edit', compact('norma'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DescriptorNormasSaveRequest $request, Norma $norma)
    {
        $descriptores_id = [];

        // 2023-05-31 XXXX
        if (is_array($request->descriptores) || is_object($request->descriptores))
        {
            foreach ($request->descriptores as $d) {
                $descriptores_id[] = $d['descriptor_id'];
            }
        }
        $norma->descriptores()->sync($descriptores_id);

        return redirect()
            ->route('backend.descriptornormas.edit', ['norma' => $norma])
            ->with('save_status', 'Descriptores guardados con éxito.');
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
     * [getBackendDescriptorJson description]
     * @return [type] [description]
     */
    public function getBackendDescriptorJson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'b' => 'string|nullable|in:normas,decretos,sesiones,todas',
            'c' => 'integer|nullable|min:0',
            't' => sprintf('required|string|min:%d', config('params.charCountBackendDescriptorJson'))
        ]);

        if ($validator->fails())
            return [
                'status' => 'ERROR',
                'message' => join($validator->errors()->all()),
                'data' => null
            ];

        $jsonData = [];
        $flag_recortado = false; // Flag para detectar resultados truncados

        // Filtro descriptores por base
        $normas_db = ($request->has('b')) ? $request->b : 'todas';

        // Cantidad de registros. Si es '0', se muestran todos
        $cantidad_registros = ($request->has('c'))
            ? $request->c
            : config('params.maxBackendDescriptorJson');

        // Busco resultados
        if ($request->has('t')) {
            $search = $request->t;

            // Preparo la consulta
            $jsonData = DB::table('descriptores as D')
                ->select('D.id as id', 'D.tag as tag');

            if (in_array($normas_db, ['normas', 'sesiones', 'decretos']))
                $jsonData = $jsonData->join('descriptor_bases as DBASES', 'D.id', '=', 'DBASES.descriptor_id')
                    ->where('DBASES.base', '=', $normas_db);

            $jsonData = $jsonData->where('D.tag', 'LIKE', "%$search%")
                ->orderBy('D.tag');

            // Si es '0', se muestran todos.
            // Sino, se obtiene un resultado de mas para cotejar luego
            // si el resultado esta "truncado" por el limit.
            if ($cantidad_registros > 0)
                $jsonData = $jsonData->limit($cantidad_registros+1);

            // Ejecuto la consulta
            $jsonData = $jsonData->get();

            // Como busco por cantidad_registros+1, quito el sobrante, si aplica
            if (($cantidad_registros > 0) && ($jsonData->count() > $cantidad_registros)) {
                $flag_recortado = true;
                $jsonData->pop();
            }
        }

        return [
            'status' => 'OK',
            'message' => '',
            'data' => [
                'filtro_cantidad' => $cantidad_registros,
                'recortado' => $flag_recortado,
                'descriptores' => $jsonData
            ]
        ];
    }

    /**
     * [addBackendDescriptorJson description]
     * @param Request $request [description]
     */
    public function addBackendDescriptorJson(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            'base' => ['required', 'string', Rule::in(['normas', 'decretos', 'sesiones'])],
            'tag' => 'required|string|min:3'
        ]);
        if ($validator->fails())
            return [
                'status' => 'ERROR',
                'message' => join($validator->errors()->all()),
                'data' => null
            ];

        $d_tag = strtoupper($request->input('tag'));
        $d_base = strtolower($request->input('base'));

        // Creo/obtengo el descriptor
        $descriptor = Descriptor::firstOrCreate(['tag' => $d_tag]);

        // Creo la relacion a la base, si no existe.
        if ($descriptor->bases()->where('base', $d_base)->count() == 0)
            $descriptor->bases()->create(['base' => $d_base]);

        return ['status' => 'OK', 'message' => '', 'data' => $descriptor];
    }
}
