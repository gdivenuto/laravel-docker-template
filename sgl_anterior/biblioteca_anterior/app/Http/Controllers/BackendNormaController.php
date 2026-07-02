<?php

namespace App\Http\Controllers;

use App\Norma;
use App\Helpers\FakeAttrib;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\NormaSaveRequest;

class BackendNormaController extends Controller
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
        return view('backend.normas.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $norma = new Norma();
        $norma_bases = FakeAttrib::basesList();
        $norma_tipo_acto = FakeAttrib::tipoActoList('normas');

        return view('backend.normas.edit', compact('norma', 'norma_bases', 'norma_tipo_acto'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NormaSaveRequest $request)
    {
        // Las validaciones estan en /app/Http/Requests/NormaSaveRequest
        $norma = Norma::create([
            'acto' => $request->acto ?? '',
            'nro' => $request->nro ?? '',
            'origen' => $request->origen ?? '',
            'nro_hcd' => $request->nro_hcd ?? '',
            'exped' => $request->exped ?? '',
            'bloque' => $request->bloque ?? '',
            'hcd_exped' => $request->hcd_exped ?? '',
            'fec_sancion' => $request->fec_sancion,
            'fec_promulga' => $request->fec_promulga,
            'fec_publica' => $request->fec_publica,
            'dec_promulga' => $request->dec_promulga ?? '',
            'boletin_nro' => $request->boletin_nro ?? '',
            'boletin_pag' => $request->boletin_pag ?? '',
            'registro_t' => $request->registro_t ?? '',
            'registro_f' => $request->registro_f ?? '',
            'abrogacion_a' => $request->abrogacion_a ?? '',
            'abrogacion_n' => $request->abrogacion_n ?? '',
            'contenido' => $request->contenido ?? '',
            'nro_tema' => $request->nro_tema ?? '',
            'alcance' => $request->alcance ?? '',
            'caracter' => $request->caracter ?? '',
            'recopila' => $request->recopila ?? '',
            'fec_incluido' => $request->fec_incluido,
            'fec_excluido' => $request->fec_excluido,
            'sin_nro' => $request->sin_nro ?? '',
            'ingresa' => $request->ingresa ?? '',
            'aprobado' => $request->aprobado ?? '',
            'ausentes' => $request->ausentes ?? '',
            'base' => $request->base,
            'usuario_id' => Auth::user()->remote_user_id,
            'usuario_codigo' => str_replace('@concejomdp.gov.ar', '', Auth::user()->email)
        ]);

        return redirect()
            ->route('backend.normas.edit', ['norma' => $norma])
            ->with('save_status', sprintf('Norma guardada con éxito (ID %d).', $norma->id));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function show(Norma $norma)
    {
        // Alias de redirect por ID al frontend
        return redirect()->route('normas.show', [
            'normas_db' => $norma->base, 
            'norma' => $norma->id
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function edit(Norma $norma)
    {
        $norma_bases = FakeAttrib::basesList();
        $norma_tipo_acto = FakeAttrib::tipoActoList($norma->base);

        return view('backend.normas.edit', compact('norma', 'norma_bases', 'norma_tipo_acto'));
    }
    
    /**
     * Se obtienen los Actos de una Base determinada
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function dtGetActosJson(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base' => 'string|in:normas,decretos,sesiones' 
        ]);

        if ($validator->fails())
            return [
                'status' => 'ERROR', 
                'message' => join($validator->errors()->all()), 
                'data' => null
            ];
        
        // Filtro actos por base
        $base = ($request->has('base')) ? $request->base : 'todas';

        if (in_array($base, ['normas', 'sesiones', 'decretos', 'todas']))
            $tipos_acto = FakeAttrib::tipoActoList($base);

        return [
            'status' => 'OK', 
            'message' => '', 
            'data' => [
                'actos' => $tipos_acto
            ]
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\NormaSaveRequest  $request
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function update(NormaSaveRequest $request, Norma $norma)
    {
        // Las validaciones estan en /app/Http/Requests/NormaSaveRequest
        $norma->fill([
            'acto' => $request->acto ?? '',
            'nro' => $request->nro ?? '',
            'origen' => $request->origen ?? '',
            'nro_hcd' => $request->nro_hcd ?? '',
            'exped' => $request->exped ?? '',
            'bloque' => $request->bloque ?? '',
            'hcd_exped' => $request->hcd_exped ?? '',
            'fec_sancion' => $request->fec_sancion,
            'fec_promulga' => $request->fec_promulga,
            'fec_publica' => $request->fec_publica,
            'dec_promulga' => $request->dec_promulga ?? '',
            'boletin_nro' => $request->boletin_nro ?? '',
            'boletin_pag' => $request->boletin_pag ?? '',
            'registro_t' => $request->registro_t ?? '',
            'registro_f' => $request->registro_f ?? '',
            'abrogacion_a' => $request->abrogacion_a ?? '',
            'abrogacion_n' => $request->abrogacion_n ?? '',
            'contenido' => $request->contenido ?? '',
            'nro_tema' => $request->nro_tema ?? '',
            'alcance' => $request->alcance ?? '',
            'caracter' => $request->caracter ?? '',
            'recopila' => $request->recopila ?? '',
            'fec_incluido' => $request->fec_incluido,
            'fec_excluido' => $request->fec_excluido,
            'sin_nro' => $request->sin_nro ?? '',
            'ingresa' => $request->ingresa ?? '',
            'aprobado' => $request->aprobado ?? '',
            'ausentes' => $request->ausentes ?? '',
            'base' => $request->base,
            'usuario_id' => Auth::user()->remote_user_id,
            'usuario_codigo' => str_replace('@concejomdp.gov.ar', '', Auth::user()->email)
        ]);

        $norma->save();

        return redirect()
            ->route('backend.normas.edit', ['norma' => $norma])
            ->with('save_status', sprintf('Norma guardada con éxito (ID %d).', $norma->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function destroy(Norma $norma)
    {
        //
    }

    /**
     * Clona una norma y sus datos relacionados para usarla
     * como plantilla de otra norma nueva.
     * 
     * @param  Norma  $norma [description]
     * @return [type]        [description]
     */
    public function clonar(Norma $norma) 
    {
        $clon = $norma->clonar();

        return redirect()
            ->route('backend.normas.edit', ['norma' => $clon])
            ->with('save_status', sprintf('Norma duplicada con éxito (ID %d).', $clon->id));
    }

    /**
     * Returns statistic data
     * 
     * @return [type] [description]
     */
    public function dtGetNormasJson() 
    {
        $data = Norma::query();

        return datatables()->eloquent($data)->toJson();
    }
}
