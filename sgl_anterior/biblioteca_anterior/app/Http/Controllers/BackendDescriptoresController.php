<?php

namespace App\Http\Controllers;

use App\Descriptor;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

use App\Http\Requests\DescriptorSaveRequest;

class BackendDescriptoresController extends Controller
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
        return view('backend.descriptores.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $descriptor = new Descriptor();
        return view('backend.descriptores.edit', compact('descriptor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DescriptorSaveRequest $request)
    {
        // Las validaciones estan en /app/Http/Requests/DescriptorSaveRequest
        $descriptor = Descriptor::create([
            'tag' => $request->tag ?? '',
        ]);
        
        // Relación para asociar el descriptor recién creado con la tabla 'descriptor_bases'
        $descriptor->bases()->create([
            'descriptor_id' => $descriptor->id,
            'base' => 'normas', // por defecto, como está definido en la DB también
        ]);
        
        return redirect()
            ->route('backend.descriptores.edit', ['descriptor' => $descriptor])
            ->with('save_status', sprintf('Descriptor guardado con éxito (ID %d).', $descriptor->id));
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
    public function edit(Descriptor $descriptor)
    {
        return view('backend.descriptores.edit', compact('descriptor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DescriptorSaveRequest $request, Descriptor $descriptor)
    {
        // Las validaciones estan en /app/Http/Requests/DescriptorSaveRequest
        $descriptor->fill([
            'tag' => $request->tag ?? '',
        ]);

        $descriptor->save();

        return redirect()
            ->route('backend.descriptores.edit', ['descriptor' => $descriptor])
            ->with('save_status', sprintf('Descriptor guardado con éxito (ID %d).', $descriptor->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Descriptor $descriptor)
    {
        try {
            $descriptor->delete();
            
            return redirect()
                ->route('backend.descriptores.index')
                ->with('save_status', 'Descriptor eliminado con éxito.');
        }
        catch (QueryException $e) {
            
            return redirect()
                ->back()
                ->withErrors(['error_message' => 'No se puede eliminar el Descriptor debido a que se encuentra asociado a una Norma.']);
        }
    }

    /**
     * Returns statistic data
     * 
     * @return [type] [description]
     */
    public function dtGetDescriptoresJson() 
    {
        $data = Descriptor::query();

        return datatables()->eloquent($data)->toJson();
    }

    /**
     * Retorna una vista para seleccionar qué descriptores reemplazar
     * @return [type] [description]
     */
    public function choise()
    {
        return view('backend.descriptores.choise');
    }

    /**
     * Se buscan Descriptores en base a un criterio ingresado
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function buscarDescriptores(Request $request)
    {
        $data = [];

        if ( $request->has('q') && $request->has('base') ) {
            $search = $request->q;
            $base = $request->base;

            $sugerencias = Descriptor::getDescriptorsWithOccurrences($search, $base);

            foreach ($sugerencias as $sugerencia) {
                $data[] = [
                    'id' => $sugerencia->id,
                    'text' => $sugerencia->tag.' | Ocurrencias: '.$sugerencia->ocurrencias,
                ];
            }
        }
        return response()->json($data);
    }

    /**
     * Se reemplaza un Descriptor por otro, por medio de su Id
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function replace(Request $request)
    {
        $id_descriptor_que_queda = $request->input('id_descriptor_que_queda');
        $id_descriptor_que_se_va = $request->input('id_descriptor_que_se_va');

        //dd('Se queda: '.$id_descriptor_que_queda.' - Se va: '.$id_descriptor_que_se_va);

        try {
            DB::transaction(function () use ($id_descriptor_que_queda, $id_descriptor_que_se_va) {

                // Se obtiene la fecha y hora actual
                // para los campos de created_at y updated_at
                // ------------------------------------------
                $now = \Carbon\Carbon::now();

                // Se obtienen los datos de las tablas 
                // donde es usado el descriptor que "se va"
                // ---------------------------------------
                $data_descriptor_norma = DB::table('descriptor_norma')
                    ->where('descriptor_id', $id_descriptor_que_se_va)
                    ->get();

                $data_descriptor_digesto = DB::table('descriptor_digesto')
                    ->where('descriptor_id', $id_descriptor_que_se_va)
                    ->get();

                //dd($data_descriptor_norma, $data_descriptor_digesto);

                // Se insertan en las tablas
                // con el id del descriptor "que queda"
                // ------------------------------------
                foreach ($data_descriptor_norma as $data) {
                    DB::table('descriptor_norma')
                        ->updateOrInsert(
                            [
                                'descriptor_id' => $id_descriptor_que_queda,
                                'norma_id' => $data->norma_id
                            ],
                            [
                                'descriptor_id' => $id_descriptor_que_queda,
                                'norma_id' => $data->norma_id,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        );
                }

                foreach ($data_descriptor_digesto as $data) {
                    DB::table('descriptor_digesto')
                        ->updateOrInsert(
                            ['descriptor_id' => $id_descriptor_que_queda],
                            [
                                'descriptor_id' => $id_descriptor_que_queda,
                                'digesto_id' => $data->digesto_id,
                                'condicion' => $data->condicion,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]
                        );
                }

                // Se eliminan las referencias 
                // donde es usado el descriptor que "se va"
                // ---------------------------------------
                DB::table('descriptor_norma')
                    ->where('descriptor_id', $id_descriptor_que_se_va)
                    ->delete();

                DB::table('descriptor_digesto')
                    ->where('descriptor_id', $id_descriptor_que_se_va)
                    ->delete();

                DB::table('descriptor_bases')
                    ->where('descriptor_id', $id_descriptor_que_se_va)
                    ->delete();

                // Se elimina de la tabla 'descriptores' el que "se va"
                $descriptor = Descriptor::find($id_descriptor_que_se_va);

                if ($descriptor) {
                    $descriptor->delete();
                } else {
                    throw new \Exception('No se ha encontrado el descriptor.');
                }
            });

            return redirect()
                ->back()
                ->with('save_status', 'Reemplazo realizado correctamente.');

        } catch(Exception $e) {
            return redirect()
                ->back()
                ->withErrors('error_message', 'No se ha podido realizar el reemplazo.');
        }
    }

}
