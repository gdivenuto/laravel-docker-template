<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Helpers\IntranetRsc;

use App\Norma;
use App\Descriptor;
use App\Digesto;
use App\Intendencia;

use Barryvdh\DomPDF\Facade as PDF;

class NormaController extends Controller
{
    /**
     * Guarda los parametros principales de la vista en variables de sesion.
     * 
     * @param  Request $request [description]
     * @param  [type]  $from    [description]
     * @return [type]           [description]
     */
    private function saveSearchParams(Request $request, $from = 'default')
    {
        // Guardar en sesion los filtros customizados, o su valor por defecto, si aplica.

        if ($request->has('params')) {
            session(['params_'.$from => $request->params]);
        } else {
            // Si la variable de sesion se setea como 'null', se considera que ese filtro no se aplica.
            session(['params_'.$from => null]);
        }

        // Fuerzo el guardado de la sesion (para que no tenga un 'salvado diferido' segun el driver)
        $request->session()->save();

        return true;
    }

    /**
     * Aplica los filtros en base a la 'base de datos' o 'digesto' para una lista de normas.
     * Devuelve una instancia de Illuminate\Database\Eloquent\Builder asociada a Norma.
     * @param  [type] $normas_db     [description]
     * @param  [type] $norma_id_list [description]
     * @return [type]                [description]
     */
    private function applyDigestoFilter($normas_db, $norma_id_list) {
        // Preparo resultado
        // Se utiliza 'whereIntegerInRaw' para acelerar la consulta (MySQL PDO 'in-bug').
        // IMPORTANTE: Ademas, si no hago el fix de 'distinct + norma.*', el query me 
        // devuelve registros duplicados debido a los multiple joins de los digestos 
        // dinamicos (por los descriptores AND y OR). Ver el 'return' al final del metodo.
        $builder = Norma::with('descriptores:id,tag')
            ->distinct()
            ->select('normas.id')
            ->whereIntegerInRaw('id', $norma_id_list);

        // Si es 'todas', no filtro nada
        if ($normas_db != 'todas') {

            // Si es una de las bases 'por defecto', agrego el criterio
            if (in_array($normas_db, ['normas','sesiones','decretos'])) {
                $builder = $builder->where('base', '=', $normas_db);
            } else {

                // Si no es ninguna de las anteriores, es un digesto.
                // Si no encuentro el digesto (null) lo considero como 'todas'
                if ($digesto = Digesto::where('nombre', '=', $normas_db)->first()) {
                    
                    // Filtro por condiciones base
                    $filtro = json_decode($digesto->filtro) ?? [];
                    if (count($filtro) > 0) $builder = $builder->where($filtro);
                    $alias_counter = 0;

                    // ---- Descriptores AND
                    if ($digesto->descriptores_and->count() > 0) {
                        $desc_and_id_list = $digesto->descriptores_and->sortBy('id')->pluck('id')->toArray();

                        foreach ($desc_and_id_list as $d_id) {
                            $builder = $builder->join("descriptor_norma as DN_$alias_counter", function($join) use ($alias_counter, $d_id) {
                                $join->on('normas.id', '=', "DN_$alias_counter.norma_id")
                                    ->where("DN_$alias_counter.descriptor_id", '=', $d_id);
                            });
                            $alias_counter++;
                        }
                    }

                    // ---- Descriptores OR
                    if ($digesto->descriptores_or->count() > 0) {
                        // Los descriptores obligatorios (AND) se agregan a la lista de opcionales.
                        // Esto hace que al buscar por un descriptor obligatorio, si no tiene ocurrencia en los
                        // opcionales, aparezca igual.
                        $desc_or_id_list = array_merge(
                            $digesto->descriptores_or->sortBy('id')->pluck('id')->toArray(),
                            $digesto->descriptores_and->sortBy('id')->pluck('id')->toArray()
                        );
                        sort($desc_or_id_list);
                        $builder = $builder->join("descriptor_norma as DN_$alias_counter", function($join) use ($alias_counter, $desc_or_id_list) {
                            $join->on('normas.id', '=', "DN_$alias_counter.norma_id")
                                ->whereIntegerInRaw("DN_$alias_counter.descriptor_id", $desc_or_id_list);
                        });
                    }
                } // from --> if ($digesto = Digesto::where('nombre', '=', $normas_db)->first()) {
            } // from --> if (in_array($normas_db, ['normas','sesiones','decretos'])) {
        } // from --> if ($normas_db != 'todas') {
                
        // Hago un prefetch de las normas (el id) para luego usar esa lista en el filtro 
        // que devuelve las normas completas, para evitar errores en la paginacion por el 
        // bug de paginate() + distinct().
        $normas_candidatas = $builder->get()->pluck('id')->toArray();
        return Norma::with('descriptores:id,tag')
            ->select('normas.*')
            ->whereIntegerInRaw('id', $normas_candidatas);
    }

    /**
     * Genera un array con la lista de opciones para menu de cambio de base.
     * @return [type] [description]
     */
    private function getBasesDigestosArray() {
        // ---- Lista de opciones para menu de cambio de base
        $bases = Digesto::with('descriptores:id,tag');
            
        // Si estoy en modo 'NO-BACKEND', filtro por digesto publicado
        if (! config('params.backend_enabled'))
            $bases = $bases->where('publicado', true);
            
        $bases = $bases->orderBy('nombre')
            ->get()
            ->pluck('nombre');

        foreach (['normas', 'sesiones', 'decretos'] as $b) $bases->prepend($b);
        $bases->push('todas');
        
        return $bases->toArray();
    }

    /**
     * Verifica que el parametro 'normas_db' se corresponda a un identificador válido.
     * Caso contrario, aborta.
     * @param  [type] $normas_db [description]
     * @return [type]            [description]
     */
    private function checkNormaDB($normas_db) {
        $n = ($normas_db) ?? 'normas';
        if (in_array($n, $this->getBasesDigestosArray(), true))
            return $n;
        else
            abort(404);
    }

    /**
     * [generateLikeQuery description]
     * @param  [type] $terms [description]
     * @return [type]        [description]
     */
    private function generateLikeQuery($terms, $normas_db = 'normas') {
        // ---- Armo el query
        $query = '';
        foreach ($terms as $idx => $t) {
            if ($idx == 0) {
                // Query inicial, el "core" -----------------------------------
                $query = <<<SQL
SELECT DISTINCT N_{$idx}.id, N_{$idx}.origen, N_{$idx}.nro_hcd, N_{$idx}.exped, N_{$idx}.bloque, N_{$idx}.hcd_exped, N_{$idx}.fec_sancion, N_{$idx}.fec_promulga, N_{$idx}.fec_publica, N_{$idx}.dec_promulga, N_{$idx}.boletin_nro, N_{$idx}.boletin_pag, N_{$idx}.abrogacion_a, N_{$idx}.abrogacion_n, N_{$idx}.contenido, N_{$idx}.nro_tema, N_{$idx}.recopila, N_{$idx}.sin_nro, N_{$idx}.ingresa, N_{$idx}.procesa, N_{$idx}.aprobado, N_{$idx}.acto_nro, N_{$idx}.base
FROM    normas AS N_{$idx}
SQL;
                // ------------------------------------------------------------
            } else {
                // Queries "externos" -----------------------------------------
                $query = <<<SQL
SELECT DISTINCT N_{$idx}.*
FROM    ({$query}) as N_{$idx}
SQL;
                // ------------------------------------------------------------
            }

            // WHERE clause ---------------------------------------------------
            $query = <<<SQL
{$query}
LEFT JOIN descriptor_norma DN_{$idx} ON (N_{$idx}.id = DN_{$idx}.norma_id)
JOIN descriptores D_{$idx} ON (D_{$idx}.id = DN_{$idx}.descriptor_id)
LEFT JOIN relaciones R_{$idx} ON (N_{$idx}.id = R_{$idx}.norma_id)
LEFT JOIN observaciones O_{$idx} ON (N_{$idx}.id = O_{$idx}.norma_id)
WHERE
(
       N_{$idx}.origen LIKE '%{$t}%'
    OR N_{$idx}.nro_hcd LIKE '%{$t}%'
    OR N_{$idx}.exped LIKE '%{$t}%'
    OR N_{$idx}.bloque LIKE '%{$t}%'
    OR N_{$idx}.fec_sancion LIKE '%{$t}%'
    OR N_{$idx}.fec_promulga LIKE '%{$t}%'
    OR N_{$idx}.fec_publica LIKE '%{$t}%'
    OR N_{$idx}.boletin_nro LIKE '%{$t}%'
    OR N_{$idx}.boletin_pag LIKE '%{$t}%'
    OR N_{$idx}.abrogacion_a LIKE '%{$t}%'
    OR N_{$idx}.abrogacion_n LIKE '%{$t}%'
    OR N_{$idx}.contenido LIKE '%{$t}%'
    OR N_{$idx}.nro_tema LIKE '%{$t}%'
    OR N_{$idx}.sin_nro LIKE '%{$t}%'
    OR N_{$idx}.ingresa LIKE '%{$t}%'
    OR N_{$idx}.aprobado LIKE '%{$t}%'
    OR N_{$idx}.acto_nro LIKE '%{$t}%'
    OR D_{$idx}.tag LIKE '%{$t}%'
    OR R_{$idx}.n LIKE '%{$t}%'
    OR R_{$idx}.p LIKE '%{$t}%'
    OR O_{$idx}.obs LIKE '%{$t}%'
    
SQL;

            // Agregar filtro de base de datos (normas/digesto/decretos)
            // Atencion: Se agrega el parentesis de cierre para los OR anteriores
            // if ($normas_db != 'todas') {
            //     $query = ($normas_db == 'digesto')
            //         ? $query . ") AND N_{$idx}.base = 'normas' AND N_{$idx}.dec_promulga <> 'esp-pro' AND N_{$idx}.recopila = 's'"
            //         : $query . " OR N_{$idx}.dec_promulga LIKE '%{$t}%' OR N_{$idx}.recopila LIKE '%{$t}%') AND N_{$idx}.base = '{$normas_db}' ";
            // } else {
            //     $query = $query . " OR N_{$idx}.dec_promulga LIKE '%{$t}%' OR N_{$idx}.recopila LIKE '%{$t}%')";
            // }
            if (in_array($normas_db, ['normas','sesiones','decretos']) )
                $query .= " OR N_{$idx}.dec_promulga LIKE '%{$t}%' OR N_{$idx}.recopila LIKE '%{$t}%') AND N_{$idx}.base = '{$normas_db}' ";
            else
                // "todas" o cualquier digesto
                $query .= " OR N_{$idx}.dec_promulga LIKE '%{$t}%' OR N_{$idx}.recopila LIKE '%{$t}%')";

            // ----------------------------------------------------------------
        }
        return $query;
    }

    /**
     * Se obtiene un array con los archivos (de Proyectos) de un Expediente del HCD
     * PHP nativo !! Porque el directorio se encuentra fuera del proyecto de Biblioteca,
     * ver luego si existe una forma utilizando el facade Storage con directorios externos.
     * @return array $archivos
     */
    public function getProyectos($hcd_exped)
    {
        $partes = explode("/", $hcd_exped);
        
        $anio = $partes[0];
        $numero = $partes[1];

        $codificado = substr($anio, -2)."E0".$numero;

        // Se ha montado a este recurso
        $ruta = "/var/www/sgl/expedientes/proyectos/$anio/$codificado/electronico/";

        // Se hace referencia a hcd02 porque se utiliza solamente de forma interna (biblioteca)
        $url = "http://hcd02.concejomdp.gov.ar/sgl/expedientes/proyectos/$anio/$codificado/electronico/";
        
        $archivos = [];
        // Si el directorio del expediente respectivo existe
        if (is_dir($ruta)) {
            if ($handle = opendir($ruta)) {
                while (false !== ($file = readdir($handle)))
                    // Sólo se toman los archivos, no los directorios internos
                    //if ( ! in_array($file, ['.', '..', 'electronico', 'reservados'])
                    if ( $file != '.' && $file != '..' && $file != 'electronico' && $file != 'reservados' )
                        $archivos[] = $file;

                closedir($handle);

                rsort($archivos);
            }
        }

        return [$url, $archivos];
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function show($normas_db, Norma $norma)
    {
        $normas_db = $this->checkNormaDB($normas_db);

        if ($norma->acto === "or") {
            $norma_ordenanza_anterior = Norma::where('acto', $norma->acto)->where('nro', intval($norma->nro - 1))->first();
            $norma_ordenanza_siguiente = Norma::where('acto', $norma->acto)->where('nro', intval($norma->nro + 1))->first();
        } else {
            $norma_ordenanza_anterior = null;
            $norma_ordenanza_siguiente = null;
        }

        $documentos = (isset($norma->hcd_exped) && $norma->hcd_exped != '') 
            ? $this->getProyectos($norma->hcd_exped)
            : null;

        return view('normas.show')
            ->with('normas_db', $normas_db)
            ->with('norma', $norma)
            ->with('documentos', $documentos)
            ->with('norma_ordenanza_anterior', $norma_ordenanza_anterior)
            ->with('norma_ordenanza_siguiente', $norma_ordenanza_siguiente);
    }

    /**
     * [showByActoNumero description]
     * @param  [type] $normas_db [description]
     * @param  [type] $acto      [description]
     * @param  [type] $nro       [description]
     * @return [type]            [description]
     */
    public function showByActoNumero($normas_db, $acto, $nro, $force_digesto_id = null)
    {
        $force_digesto_id = ($force_digesto_id) ?? '';
        //$normas_db = ($normas_db) ?? 'normas';
        $normas_db = $this->checkNormaDB($normas_db);
        $norma = Norma::where('acto', $acto)->where('nro', $nro)->firstOrFail();

        if ($force_digesto_id == '') {
            // Logica normal, sin forzado de digesto: esto se utiliza cuando 
            // se accede al URL desde la aplicacion.
            return view('normas.show', compact('normas_db', 'norma'));
        } else {
            // Logica de digesto forzada: esto se utiliza cuando se accede al URL
            // de forma 'externa', por ejemplo, con un codigo QR.
            $digesto = Digesto::where('id', $force_digesto_id)->firstOrFail();
            return redirect()->route('normas.show', ['normas_db' => $digesto->nombre, 'norma' => $norma]);
        }
    }

    /**
     * [getByActoNumeroJson description]
     * @param  [type] $normas_db [description]
     * @param  [type] $acto      [description]
     * @param  [type] $nro       [description]
     * @return [type]            [description]
     */
    public function getByActoNumeroJson($normas_db, $acto, $nro)
    {
        $normas_db = ($normas_db) ?? 'normas';
        $ret = [];
        $norma = Norma::where('acto', $acto)->where('nro', $nro)->first();
        if ($norma) {
            $ret = [
                'status' => 'OK',
                'message' => 'Norma encontrada con éxito.',
                'data' => $norma
            ];
        } else {
            $ret = [
                'status' => 'WARNING',
                'message' => 'La norma solicitada no existe en nuestra base de datos.',
                'data' => null
            ];
        }

        return response()->json($ret);
    }    

    /**
     * [searchSimple description]
     * @param  [type] $descriptor_id [description]
     * @return [type]                [description]
     */
    public function searchSimple($normas_db = 'normas', $descriptor_id = null)
    {
        $default_descriptors = null;
        $descriptor_logic = 'or';
        if ($descriptor_id) {
            $d = Descriptor::findOrFail($descriptor_id);
            $default_descriptors = [[ 'id' => $d->id, 'tag' => $d->tag ]];
        }
        
        $bases = $this->getBasesDigestosArray();
        $digesto = Digesto::where('nombre', $normas_db)->first();
        return view('normas.searchdescriptor', compact('normas_db', 'descriptor_logic', 'default_descriptors', 'bases', 'digesto'));
    }

    /**
     * [searchSimpleTag description]
     * @param  string $normas_db        [description]
     * @param  string $descriptor_logic [description]
     * @param  string $tag_str          [description]
     * @return [type]                   [description]
     */
    public function searchSimpleTag($normas_db = 'normas', $descriptor_logic = 'or', $tag_str = '')
    {
        $tag_list = explode('|', $tag_str);
        $default_descriptors = Descriptor::whereIn('tag', $tag_list)
            ->get()
            ->map(function ($d) {
                return [ 'id' => $d->id, 'tag' => $d->tag ];
            })
            ->toArray();
        
        $bases = $this->getBasesDigestosArray();
        $digesto = Digesto::where('nombre', $normas_db)->first();
        return view('normas.searchdescriptor', compact(
            'normas_db',
            'descriptor_logic',
            'default_descriptors',
            'bases', 
            'digesto'
        ));
    }

    /**
     * Formulario de busqueda avanzada
     *
     * @return \Illuminate\Http\Response
     */
    public function search($normas_db = 'normas')
    {
        $intendencias = Intendencia::all()->map(function($i,$k) { 
            return [
                'value' => $i->id, 
                'text' => sprintf("%s, %d° mandato, desde %s hasta %s", $i->intendente, $i->nro, $i->fec_desde, $i->fec_hasta ?? 'hoy')
            ]; 
        });

        $bases = $this->getBasesDigestosArray();
        $digesto = Digesto::where('nombre', $normas_db)->first();
        return view('normas.search', compact('normas_db', 'intendencias', 'bases', 'digesto'));
    }

    /**
     * Formulario de busqueda por contenido
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByContent()
    {
        return view('normas.searchcontent');
    }

    /**
     * Formulario de busqueda por palabra clave
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByKeyword($normas_db = 'normas')
    {
        $bases = $this->getBasesDigestosArray();
        $digesto = Digesto::where('nombre', $normas_db)->first();

        // Si vengo de 'searchByKeywordRedirect', recupero el criterio de la 
        // sesion y lo elimino.
        $redirect_criteria = session('keyword_redirect_criteria', null);
        request()->session()->forget('keyword_redirect_criteria');

        return view('normas.searchkeyword', compact('normas_db', 'bases', 'digesto', 'redirect_criteria'));
    }

    /**
     * Redirect al formulario de busqueda por palabra clave
     *
     * @return \Illuminate\Http\Response
     */
    public function searchByKeywordRedirect(Request $request)
    {
        // Validación de parametros -------------------------------------------
        $request->validate([
            'criteria_args' => 'required|string|regex:/^[a-z0-9áéíóúüñÁÉÍÓÚÜÑ+\-\.@\(\)~" ]+$/i'
        ]);

        // Guardo parametro y hago una redirección a 'searchByKeyword'
        session(['keyword_redirect_criteria' => $request->criteria_args]);

        return redirect()->route('normas.searchkeyword', ['normas_db' => 'normas']);
    }

    /**
     * [normasVenc description]
     * @return [type] [description]
     */
    public function normasVenc()
    {
        return view('normas.normasvenc');
    }

    /**
     * [clearSessionSearchJson description]
     * @param  string $search_type [description]
     * @return [type]              [description]
     */
    public function clearSessionSearchJson($search_type = '') 
    {
        // Elimino una variable de sesion para busquedas, si existe.
        if (request()->session()->has('params_'.$search_type)) {
            session([
                'params_'.$search_type => null
            ]);
        }

        // Fuerzo el guardado de la sesion (para que no tenga un 'salvado diferido' segun el driver)
        request()->session()->save();

        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => null
        ]);
    }

    /**
     * [getNormaJson description]
     * @param  Norma  $norma [description]
     * @return [type]        [description]
     */
    public function getNormaJson($norma_id)
    {
        $resultado = [];
        try {
            $norma = Norma::findOrFail($norma_id);
            $resultado = [
                'status' => 'OK',
                'message' => 'OK',
                'data' => $norma->load('actas', 'descriptores', 'relaciones', 'abstenciones', 'observaciones')
            ];
        } catch (Exception $e) {
            $resultado = [
                'status' => 'ERROR',
                'message' => 'Error al obtener norma.',
                'data' => null
            ];        
        }

        return response()->json($resultado);
    }

    /**
     * [getNormasByDescriptorJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getNormasByDescriptorJson(Request $request) {
        $normas_db = $request->normas_db;
        $list_descriptores = explode(',', $request->descriptor_id_list);
        $data = null;

        if ($request->descriptor_logic == 'or') {
            // "alguno" de los descriptores (OR)
            $id_list = DB::table('descriptor_norma')
                ->select('norma_id')
                ->whereIn('descriptor_id', $list_descriptores)
                ->groupBy('norma_id')
                ->get()
                ->pluck('norma_id')
                ->toArray();
        } else {
            // "todos" de los descriptores (AND)
            $id_list = DB::table('descriptor_norma')
                ->select(DB::raw('norma_id, count(*) as norma_count'))
                ->whereIn('descriptor_id', $list_descriptores)
                ->groupBy('norma_id')
                ->having('norma_count', '=', count($list_descriptores))
                ->get()
                ->pluck('norma_id')
                ->toArray();
        }

        // Filtro de base de datos de normas/digesto/sesiones
        $data = $this->applyDigestoFilter($normas_db, $id_list);
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => $data->paginate(config('params.resultsPerPage'))
        ]);
    }

    /**
     * [getNormasByDescriptorPdf description]
     * @param  Request $request   [description]
     * @param  string  $normas_db [description]
     * @return [type]             [description]
     */
    public function getNormasByDescriptorPdf(Request $request) {
        $normas_db = $request->normas_db;
        $list_descriptores = explode(',', $request->descriptor_id_list);
        $logic = $request->descriptor_logic;
        $data = null;

        if ($logic == 'or') {
            // "alguno" de los descriptores (OR)
            $id_list = DB::table('descriptor_norma')
                ->select('norma_id')
                ->whereIn('descriptor_id', $list_descriptores)
                ->groupBy('norma_id')
                ->get()
                ->pluck('norma_id')
                ->toArray();
        } else {
            // "todos" de los descriptores (AND)
            $id_list = DB::table('descriptor_norma')
                ->select(DB::raw('norma_id, count(*) as norma_count'))
                ->whereIn('descriptor_id', $list_descriptores)
                ->groupBy('norma_id')
                ->having('norma_count', '=', count($list_descriptores))
                ->get()
                ->pluck('norma_id')
                ->toArray();
        }

        // Filtro de base de datos de normas/digesto/sesiones
        $data = $this->applyDigestoFilter($normas_db, $id_list)->get();
        $descriptores = Descriptor::whereIn('id', $list_descriptores)->get()->pluck('tag')->toArray();
        
        // Respuesta PDF ------------------------------------------------------
        // Generar PDF (se hace de esta forma para poder numerar las páginas)
        $titulo = 'Búsqueda por descriptores';
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.pdfdescriptor', compact('normas_db','titulo','data','logic','descriptores'));

        return $pdf->download(sprintf('biblioteca-hcd_descriptores_%s_%s.pdf',
            str_replace(' ', '-', $normas_db),
            \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
        ));
    }

    /**
     * [getNormasAdvSearchJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getNormasAdvSearchJson(Request $request, $normas_db = 'normas') {
        
        // Validación de parametros -------------------------------------------
        $rules = [
            'params' => 'required|array',
            'params.*.type' => 'required|in:has_or,has_and,has_not,begins,ends,contains,tipo_acto,date_sancion,date_promulga,date_publica,date_intendencia,full_content',
            'params.*.value' => 'required'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error en validación de parámetros: '.implode("\n",$validator->errors()->all()),
                'data' => null
            ]);
        }

        // Validacion extra para campos de tipo "contenido" (reescribo la regla)
        $rules['params.*.value'] = 'exclude_unless:params.*.type,full_content|required|string|regex:/^[a-z0-9áéíóúüñÁÉÍÓÚÜÑ_+\-<>@\(\)~ ]+$/i';

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error en validación de parámetros: '.implode("\n",$validator->errors()->all()),
                'data' => null
            ]);
        }
        
        // Consulta dinamica --------------------------------------------------
        // Normalizo los parametros 
        $params = [];
        foreach ($request->params as $p) {
            $type = $p['type'];
            $value = (is_array($p['value'])) ? $p['value'] : [$p['value']]; // convierto el valor a un array (si no era uno)
            $params[$type] = (array_key_exists($type, $params))
                ? array_unique(array_merge($params[$type], $value))
                : $value;
        }
        
        // Query base
        $query = DB::table('normas AS N')
            ->select('N.id AS id');

        // 'has_and' se resuelve con un subquery
        if (array_key_exists('has_and', $params)) {
            $subquery_has_and = DB::table('descriptor_norma')
                ->select('norma_id', DB::raw('count(*)'))
                ->whereIn('descriptor_id', $params['has_and'])
                ->groupBy('norma_id')
                ->having('count(*)', '=', count($params['has_and']));

            $query = $query->joinSub($subquery_has_and, 'D_HAS_AND', function ($join) {
                $join->on('N.id', '=', 'D_HAS_AND.norma_id');
            });
        }

        // 'has_or'
        if (array_key_exists('has_or', $params)) {
            $query = $query->join('descriptor_norma as D_HAS_OR', function($join) use ($params) {
                $join->on('N.id', '=', 'D_HAS_OR.norma_id')
                    ->whereIn('D_HAS_OR.descriptor_id', $params['has_or']);
            });
        }

        // 'has_not'
        if (array_key_exists('has_not', $params)) {
            $query = $query->leftJoin('descriptor_norma as D_HAS_NOT', function($join) use ($params) {
                $join->on('N.id', '=', 'D_HAS_NOT.norma_id')
                    ->whereIn('D_HAS_NOT.descriptor_id', $params['has_not']);
            })->whereNull('D_HAS_NOT.descriptor_id');
        }

        // 'begins', 'ends', 'contains'
        if (array_key_exists('begins', $params) || array_key_exists('ends', $params) || array_key_exists('contains', $params)) {
            $query = $query->join('descriptor_norma as D_STR', 'N.id', '=', 'D_STR.norma_id')
                ->join('descriptores as D', function($join) use ($params) {
                    $join->on('D.id', '=', 'D_STR.descriptor_id');
                    $join->on('D.id', '=', 'D_STR.descriptor_id')
                        ->where(function($q) use ($params) {
                            if (array_key_exists('begins', $params))
                                foreach ($params['begins'] as $v) $q = $q->orWhere('D.tag', 'LIKE', $v.'%');

                            if (array_key_exists('ends', $params))
                                foreach ($params['ends'] as $v) $q = $q->orWhere('D.tag', 'LIKE', '%'.$v);

                            if (array_key_exists('contains', $params))
                                foreach ($params['contains'] as $v) $q = $q->orWhere('D.tag', 'LIKE', '%'.$v.'%');
                        });
                });
        }
        
        // 'tipo_acto'
        if (array_key_exists('tipo_acto', $params)) {
            $query = $query->whereIn('acto', $params['tipo_acto']);
        }        

        // 'date_sancion'
        if (array_key_exists('date_sancion', $params)) {
            if ($params['date_sancion'][0] != '') $query = $query->where('fec_sancion', '>=', $params['date_sancion'][0]);
            if ($params['date_sancion'][1] != '') $query = $query->where('fec_sancion', '<=', $params['date_sancion'][1]);
        }

        // 'date_promulga'
        if (array_key_exists('date_promulga', $params)) {
            if ($params['date_promulga'][0] != '') $query = $query->where('fec_promulga', '>=', $params['date_promulga'][0]);
            if ($params['date_promulga'][1] != '') $query = $query->where('fec_promulga', '<=', $params['date_promulga'][1]);
        }

        // 'date_publica'
        if (array_key_exists('date_publica', $params)) {
            if ($params['date_publica'][0] != '') $query = $query->where('fec_publica', '>=', $params['date_publica'][0]);
            if ($params['date_publica'][1] != '') $query = $query->where('fec_publica', '<=', $params['date_publica'][1]);
        }

        // 'date_intendencia'
        if (array_key_exists('date_intendencia', $params)) {
            $intendencia = Intendencia::findOrFail($params['date_intendencia'][0]);
            $query = $query->where('fec_sancion', '>=', $intendencia->fec_desde);
            if (! is_null($intendencia->fec_hasta) ) $query = $query->where('fec_sancion', '<=', $intendencia->fec_hasta);
        }

        // 'full_content'
        /**
        if (array_key_exists('full_content', $params)) {

            $columns = implode(',', ['origen', 'nro_hcd', 'exped', 'bloque', 'hcd_exped', 'dec_promulga', 'boletin_nro', 'boletin_pag', 'abrogacion_a', 'abrogacion_n', 'contenido', 'nro_tema', 'recopila', 'sin_nro', 'ingresa', 'aprobado', 'acto_nro']);

            $query = $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)" , strtolower(trim($params['full_content'][0])));
        }
        /**/
        if (array_key_exists('full_content', $params)) {
            $searchTerm = strtolower(trim($params['full_content'][0]));
            
            // Construir las condiciones LIKE para cada columna
            $columns = ['origen', 'nro_hcd', 'exped', 'bloque', 'hcd_exped', 'dec_promulga', 'boletin_nro', 'boletin_pag', 'abrogacion_a', 'abrogacion_n', 'contenido', 'nro_tema', 'recopila', 'sin_nro', 'ingresa', 'aprobado', 'acto_nro'];
            
            $query->where(function($query) use ($columns, $searchTerm) {
                foreach ($columns as $column) {
                    $query->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                }
            });
        }

        // Obtener la consulta SQL en formato de cadena
        //$sqlQuery = $query->toSql();
        // Registro la consulta SQL en el log de Laravel
        //Log::info($sqlQuery);

        // Ahora hago la consulta con los IDs obtenidos
        $id_list = $query->get()->pluck('id')->toArray();

        // Filtro de base de datos de normas/digesto/sesiones
        $data = $this->applyDigestoFilter($normas_db, $id_list);

        $data = $data->distinct()->paginate(config('params.resultsPerPage'));

        // Genero la descripción del criterio de busqueda
        $criteria = [];
        foreach ($params as $c => $v) {
            $criteria[] = [
                'type' => $c,
                'value' => ($c == 'has_or' || $c == 'has_and' || $c == 'has_not')
                    ? Descriptor::whereIn('id', $v)->get()->pluck('tag')->toArray()
                    : $v
            ];
        }

        // Guardo los filtros seleccionados en sesion
        $this->saveSearchParams($request, 'advsearch');

        // Respuesta ----------------------------------------------------------
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => [
                'data' => $data,
                'criteria' => $criteria
            ]
        ]);
        /**/
    }

    /**
     * [getNormasAdvPdf description]
     * @param  Request $request   [description]
     * @param  string  $normas_db [description]
     * @return [type]             [description]
     */
    public function getNormasAdvPdf(Request $request, $normas_db = 'normas') {
        // Obtener parametros de sesión -------------------------------------------
        $session_params = session('params_advsearch', []);

        // si hay parametros, ejecuto la consulta
        if (is_array($session_params) && count($session_params) > 0) {

            // Consulta dinamica --------------------------------------------------
            // Normalizo los parametros 
            $params = [];
            foreach ($session_params as $p) {
                $type = $p['type'];
                $value = (is_array($p['value'])) ? $p['value'] : [$p['value']]; // convierto el valor a un array (si no era uno)
                $params[$type] = (array_key_exists($type, $params))
                    ? array_unique(array_merge($params[$type], $value))
                    : $value;
            }

            // Query base
            $query = DB::table('normas AS N')
                ->select('N.id AS id');

            // 'has_and' se resuelve con un subquery
            if (array_key_exists('has_and', $params)) {
                $subquery_has_and = DB::table('descriptor_norma')
                    ->select('norma_id', DB::raw('count(*)'))
                    ->whereIn('descriptor_id', $params['has_and'])
                    ->groupBy('norma_id')
                    ->having('count(*)', '=', count($params['has_and']));

                $query = $query->joinSub($subquery_has_and, 'D_HAS_AND', function ($join) {
                    $join->on('N.id', '=', 'D_HAS_AND.norma_id');
                });
            }

            // 'has_or'
            if (array_key_exists('has_or', $params)) {
                $query = $query->join('descriptor_norma as D_HAS_OR', function($join) use ($params) {
                    $join->on('N.id', '=', 'D_HAS_OR.norma_id')
                        ->whereIn('D_HAS_OR.descriptor_id', $params['has_or']);
                });
            }

            // 'has_not'
            if (array_key_exists('has_not', $params)) {
                $query = $query->leftJoin('descriptor_norma as D_HAS_NOT', function($join) use ($params) {
                    $join->on('N.id', '=', 'D_HAS_NOT.norma_id')
                        ->whereIn('D_HAS_NOT.descriptor_id', $params['has_not']);
                })->whereNull('D_HAS_NOT.descriptor_id');
            }

            // 'begins', 'ends', 'contains'
            if (array_key_exists('begins', $params) || array_key_exists('ends', $params) || array_key_exists('contains', $params)) {
                $query = $query->join('descriptor_norma as D_STR', 'N.id', '=', 'D_STR.norma_id')
                    ->join('descriptores as D', function($join) use ($params) {
                        $join->on('D.id', '=', 'D_STR.descriptor_id');
                        $join->on('D.id', '=', 'D_STR.descriptor_id')
                            ->where(function($q) use ($params) {
                                if (array_key_exists('begins', $params))
                                    foreach ($params['begins'] as $v) $q = $q->orWhere('D.tag', 'LIKE', $v.'%');

                                if (array_key_exists('ends', $params))
                                    foreach ($params['ends'] as $v) $q = $q->orWhere('D.tag', 'LIKE', '%'.$v);

                                if (array_key_exists('contains', $params))
                                    foreach ($params['contains'] as $v) $q = $q->orWhere('D.tag', 'LIKE', '%'.$v.'%');
                            });
                    });
            }
            
            // 'tipo_acto'
            if (array_key_exists('tipo_acto', $params)) {
                $query = $query->whereIn('acto', $params['tipo_acto']);
            }

            // 'date_sancion'
            if (array_key_exists('date_sancion', $params)) {
                if ($params['date_sancion'][0] != '') $query = $query->where('fec_sancion', '>=', $params['date_sancion'][0]);
                if ($params['date_sancion'][1] != '') $query = $query->where('fec_sancion', '<=', $params['date_sancion'][1]);
            }

            // 'date_promulga'
            if (array_key_exists('date_promulga', $params)) {
                if ($params['date_promulga'][0] != '') $query = $query->where('fec_promulga', '>=', $params['date_promulga'][0]);
                if ($params['date_promulga'][1] != '') $query = $query->where('fec_promulga', '<=', $params['date_promulga'][1]);
            }

            // 'date_publica'
            if (array_key_exists('date_publica', $params)) {
                if ($params['date_publica'][0] != '') $query = $query->where('fec_publica', '>=', $params['date_publica'][0]);
                if ($params['date_publica'][1] != '') $query = $query->where('fec_publica', '<=', $params['date_publica'][1]);
            }

            // 'date_intendencia'
            if (array_key_exists('date_intendencia', $params)) {
                $intendencia = Intendencia::findOrFail($params['date_intendencia'][0]);
                $query = $query->where('fec_sancion', '>=', $intendencia->fec_desde);
                if (! is_null($intendencia->fec_hasta) ) $query = $query->where('fec_sancion', '<=', $intendencia->fec_hasta);
            }
           
            // 'full_content'
            /**
            if (array_key_exists('full_content', $params)) {

                $columns = implode(',', ['origen', 'nro_hcd', 'exped', 'bloque', 'hcd_exped', 'dec_promulga', 'boletin_nro', 'boletin_pag', 'abrogacion_a', 'abrogacion_n', 'contenido', 'nro_tema', 'recopila', 'sin_nro', 'ingresa', 'aprobado', 'acto_nro']);

                $query = $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)" , strtolower(trim($params['full_content'][0])));
            }
            /**/
            if (array_key_exists('full_content', $params)) {
                $searchTerm = strtolower(trim($params['full_content'][0]));
                
                // Construir las condiciones LIKE para cada columna
                $columns = ['origen', 'nro_hcd', 'exped', 'bloque', 'hcd_exped', 'dec_promulga', 'boletin_nro', 'boletin_pag', 'abrogacion_a', 'abrogacion_n', 'contenido', 'nro_tema', 'recopila', 'sin_nro', 'ingresa', 'aprobado', 'acto_nro'];
                
                $query->where(function($query) use ($columns, $searchTerm) {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'LIKE', '%' . $searchTerm . '%');
                    }
                });
            }

            // Ahora hago la consulta con los IDs obtenidos
            $id_list = $query->get()->pluck('id')->toArray();

            // Filtro de base de datos de normas/digesto/sesiones
            $data = $this->applyDigestoFilter($normas_db, $id_list)->distinct()->get();

            // Genero la descripción del criterio de busqueda
            $criteria = [];
            foreach ($params as $c => $v) {
                $criteria[] = [
                    'type' => $c,
                    'value' => ($c == 'has_or' || $c == 'has_and' || $c == 'has_not')
                        ? Descriptor::whereIn('id', $v)->get()->pluck('tag')->toArray()
                        : $v
                ];
            }

            // Respuesta PDF ------------------------------------------------------
            // Generar PDF (se hace de esta forma para poder numerar las páginas)
            $titulo = 'Búsqueda Avanzada';
            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('pdf.pdfsearch', compact('normas_db','titulo','criteria','data'));

            return $pdf->download(sprintf('biblioteca-hcd_busqueda-avanzada_%s_%s.pdf',
                str_replace(' ', '-', $normas_db),
                \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
            ));
        } else 
            return response()->json(['error' => 'Forbidden.'], 403);
    }

    /**
     * [getNormasKeywordSearchJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getNormasKeywordSearchJson(Request $request, $normas_db = 'normas') {
        // Validación de parametros -------------------------------------------
        $rules = [
            'params' => 'required|array',
            'params.criteria' => 'required|array',
            'params.criteria.*' => 'required|string|regex:/^[a-z0-9áéíóúüñÁÉÍÓÚÜÑ+\-\.@\(\)~ ]+$/i',
            'params.searchonsearch' => 'required|boolean'
        ];
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Error en validación de parámetros: '.implode("\n",$validator->errors()->all()),
                'data' => null
            ]);
        }

        // Guardo los filtros seleccionados en sesion
        $this->saveSearchParams($request, 'keywordsearch');

        // Consulta -----------------------------------------------------------
        $query = $this->generateLikeQuery($request->params['criteria'], $normas_db);

        $id_list = collect(DB::select(DB::raw($query)))->pluck('id')->toArray();

        // Filtro de base de datos de normas/digesto/sesiones
        $data = $this->applyDigestoFilter($normas_db, $id_list);
        
        $data = $data->paginate(config('params.resultsPerPage'), ['normas.*']);

        // Respuesta ----------------------------------------------------------
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => $data
        ]);
    }

    /**
     * [getNormasKeywordPdf description]
     * @param  Request $request   [description]
     * @param  string  $normas_db [description]
     * @return [type]             [description]
     */
    public function getNormasKeywordPdf(Request $request, $normas_db = 'normas') {
        // Obtener parametros de sesión -------------------------------------------
        $session_params = session('params_keywordsearch', []);

        // si hay parametros, ejecuto la consulta
        if (is_array($session_params) && count($session_params) > 0) {
            // Consulta -----------------------------------------------------------
            $query = $this->generateLikeQuery($session_params['criteria'], $normas_db);
            $id_list = collect(DB::select(DB::raw($query)))->pluck('id')->toArray();

            // Filtro de base de datos de normas/digesto/sesiones
            $data = $this->applyDigestoFilter($normas_db, $id_list)->get();
            
            // Respuesta PDF ------------------------------------------------------
            // Generar PDF (se hace de esta forma para poder numerar las páginas)
            $titulo = 'Búsqueda por palabra clave';
            $pdf = \App::make('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView('pdf.pdfkeyword', compact('normas_db','titulo','session_params','data'));

            return $pdf->download(sprintf('biblioteca-hcd_palabras-clave_%s_%s.pdf',
                str_replace(' ', '-', $normas_db),
                \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
            ));
        } else 
            return response()->json(['error' => 'Forbidden.'], 403);
    }

    /**
     * [generateDigestoTextFileJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function generateDigestoTextFileJson(Request $request) {
        $output = 'digesto.txt';

        if (Storage::disk('public')->exists($output)) 
            Storage::disk('public')->delete($output);

        // Cabecera
        Storage::disk('public')->append($output, 
            "\xEF\xBB\xBF" . // UTF BOM header
            "#\n" .
            sprintf("# %s v%s - Digesto Referencial\n", config('app.name'), config('app.version')) .
            sprintf("# %s\n", date('d-m-Y h:i:s')) .
            "#\n" .
            sprintf("# Fuente: %s\n", IntranetRsc::forceHttps(config('app.url').Storage::url($output))) .
            "#\n"
        );

        // Cuerpo
        //$data = Norma::with(['hcd_expedientes', 'relaciones', 'observaciones'])
        $data = Norma::with(['relaciones', 'observaciones'])
            ->where([
                ['base', '=', 'normas'],
                ['dec_promulga', '<>', 'esp-pro'],
                ['recopila', '=', 's']
            ])
            ->whereNotNull('fec_sancion')
            ->orderBy('fec_sancion')
            ->get();

        $current_date_block = '';

        foreach ($data as $norma) {
            // fecha sancion
            $fec_sancion =  (!is_null($norma->fec_sancion))
                ? \Carbon\Carbon::parse($norma->fec_sancion)->format('d/m/Y') 
                : '(sin fecha)';

            if ($fec_sancion != '(sin fecha)' && $current_date_block != $fec_sancion) {
                $current_date_block = $fec_sancion;
                $separator = sprintf("-----[ %s ]-------------------------------------------------------------\n\n", $current_date_block);
            } else {
                $separator = "----------------------------------------\n\n";
            }

            // expedientes hcd
            // $hcd_expedientes = $norma->hcd_expedientes->pluck('hcd_exped')->join(' ');
            // $hcd_expedientes = (trim($hcd_expedientes) != '')
            //     ? $hcd_expedientes
            //     : '(no posee)';

            // boletin
            if (!empty($norma->boletin_nro) || !empty($norma->boletin_pag)) {
                $boletin = sprintf("Boletín Nro.: %s - pag. %s\n\n", 
                    (!empty($norma->boletin_nro) && $norma->boletin_nro != 'null') ? $norma->boletin_nro : '(sin número)',
                    (!empty($norma->boletin_pag) && $norma->boletin_pag != 'null') ? $norma->boletin_pag : '(sin página)'
                );
            } else {
                $boletin = '';
            }

            // abroga
            if (!empty($norma->abrogacion_a) || !empty($norma->abrogacion_n)) {
                $abrogacion = sprintf("Abroga: %s %s\n\n", 
                    $norma->abrogacion_a_desc, 
                    $norma->abrogacion_n
                );
            } else {
                $abrogacion = '';
            }

            // observaciones
            $observaciones = $norma->observaciones->pluck('obs')->join('\n');
            $observaciones = (trim($observaciones) != '')
                ? sprintf("Observaciones: %s\n\n", $observaciones)
                : '';

            // 2023-07-11
            // Se retira , $r->p, en la cadena generada, ya que no se utiliza más los Procesamientos
            // -------------------------------------------------------------------------------------
            // relaciones
            $relaciones = $norma->relaciones->map(function ($r) {
                    return sprintf("%s: %s %s", $r->relacion_desc, $r->acto_desc, $r->n);
                })
                ->join("\n");

            // Dump data to output
            Storage::disk('public')->append($output, 
                sprintf(
                    // ---- text
                    "%s" .
                    "Nro. Interno: %s\n\n" .
                    "Expediente H.C.D.: %s %s\n\n" .
                    "Acto: %s %s - Expediente: %s\n\n" .
                    "Sanción: %s - Promulgación: %s - Publicación: %s\n\n" .
                    "%s" .
                    "%s" .
                    "CONTENIDO: %s\n\n" .
                    "%s" .
                    "%s",
                    // --- data
                    $separator,
                    (!empty($norma->nro_hcd)) ? $norma->nro_hcd : '(no posee)',
                    (!empty($norma->hcd_exped)) ? $norma->hcd_exped : '(no posee)',// Ahora es una relación 1:1 //$hcd_expedientes, 
                    (trim($norma->bloque) != '') ? sprintf("Dígito: %s", Str::upper($norma->bloque)) : '',
                    $norma->acto_desc, 
                    $norma->nro,
                    $norma->exped,
                    $fec_sancion,
                    (!is_null($norma->fec_promulga)) ? \Carbon\Carbon::parse($norma->fec_promulga)->format('d/m/Y') : '(sin fecha)',
                    (!is_null($norma->fec_publica)) ? \Carbon\Carbon::parse($norma->fec_publica)->format('d/m/Y') : '(sin fecha)',
                    $boletin,
                    $abrogacion,
                    $norma->contenido,
                    (trim($relaciones) != '') ? $relaciones."\n\n" : '',
                    $observaciones
                )
            );
        }

        // Pie
        Storage::disk('public')->append($output, 
            "#\n" .
            sprintf("# Finalizado @ %s\n", date('d-m-Y h:i:s')) .
            "#\n"
        );

        // Respuesta ----------------------------------------------------------
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => IntranetRsc::forceHttps(config('app.url').Storage::url($output))
        ]);
    }

    /**
     * [getNormasAVencerJson description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function getNormasAVencerJson() {
        // Obtener normas a vencer entre hoy y 'params.normaVencDayCount' días
        $data = Norma::with('descriptores:id,tag')
            ->where('base', '=', 'normas')
            ->whereNotNull('fec_excluido')
            ->whereBetween('fec_excluido', [
                \Carbon\Carbon::now()->startOfDay()->toDateString(),
                \Carbon\Carbon::now()->endOfDay()->addDay(config('params.normaVencDayCount'))->toDateString(),
            ])
            ->orderBy('fec_excluido')
            ->paginate(config('params.resultsPerPage'));

        // Respuesta ----------------------------------------------------------
        return response()->json([
            'status' => 'OK',
            'message' => '',
            'data' => $data
        ]);
    }

    /**
     * [getNormasAVencerPdf description]
     * @return [type] [description]
     */
    public function getNormasAVencerPdf() {
        // Consulta -----------------------------------------------------------
        // Obtener normas a vencer entre hoy y 'params.normaVencDayCount' días
        $data = Norma::with('descriptores:id,tag')
            ->where('base', '=', 'normas')
            ->whereNotNull('fec_excluido')
            ->whereBetween('fec_excluido', [
                \Carbon\Carbon::now()->startOfDay()->toDateString(),
                \Carbon\Carbon::now()->endOfDay()->addDay(config('params.normaVencDayCount'))->toDateString(),
            ])
            ->orderBy('fec_excluido')
            ->get();
        
        // Respuesta PDF ------------------------------------------------------
        // Generar PDF (se hace de esta forma para poder numerar las páginas)
        $titulo = 'Vencimientos';
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.pdfvencimientos', compact('titulo','data'));

        return $pdf->download(sprintf('biblioteca-hcd_vencimientos_%s.pdf',
            \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
        ));
    }

    /**
     * Genera una ficha en PDF con la norma.
     *
     * @param  \App\Norma  $norma
     * @return \Illuminate\Http\Response
     */
    public function showPdf($normas_db, Norma $norma)
    {
        $normas_db = $this->checkNormaDB($normas_db);
        $qr = QrCode::format('png')
            ->size(100)
            ->margin(1)
            ->generate(route('normas.show', [ 'normas_db' => $normas_db, 'norma' => $norma ]));

        // Respuesta PDF ------------------------------------------------------
        // Generar PDF (se hace de esta forma para poder numerar las páginas)
        $titulo = sprintf('Ficha actualizada al %s', \Carbon\Carbon::now()->format('d/m/Y H:i:s'));
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.pdfshow', compact('normas_db','norma','titulo','qr'));

        return $pdf->download(sprintf('ficha_%s_%s.pdf',
            $norma->id,
            \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
        ));
    }

    /**
     * 2023-06-02
     * Genera un PDF con la norma en formato TRANSP(winisis).
     * @param  [type] $normas_db [description]
     * @param  Norma  $norma     [description]
     * @return [type]            [description]
     */
    public function showTransporte($normas_db, Norma $norma)
    {
        $normas_db = $this->checkNormaDB($normas_db);
        $qr = QrCode::format('png')
            ->size(100)
            ->margin(1)
            ->generate(route('normas.show', [ 'normas_db' => $normas_db, 'norma' => $norma ]));

        // Respuesta PDF ------------------------------------------------------
        // Generar PDF (se hace de esta forma para poder numerar las páginas)
        
        $titulo = '';// No se necesita mostrar un título

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pdf.pdftransporte', compact('normas_db','norma','titulo','qr'));

        return $pdf->download(sprintf('formato_transporte_%s_%s.pdf',
            $norma->id,
            \Carbon\Carbon::now()->format('Y-m-d-H-i-s')
        ));
    }
    /**/
}
