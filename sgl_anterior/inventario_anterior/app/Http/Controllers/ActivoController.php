<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

use Yajra\Datatables\Datatables;
use DB;

use Validator;

use App\Http\Requests\ActivoSaveRequest;

use App\Helpers\FakeAttrib;

use App\Activo;
use App\ActivoTipo;
use App\Grupo;

use Barryvdh\DomPDF\Facade as PDF;

class ActivoController extends Controller
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
     * Guarda los parametros principales de la vista en variables de sesion.
     *
     * @return [type] [description]
     */
    private function saveIndexParams(Request $request)
    {
        // Guardar en sesion los filtros customizados, o su valor por defecto, si aplica.
        // Si la variable de sesion se setea como 'null', se considera que ese filtro no se aplica.
        session([
            'activo_f_grupo_id'         => ($request->has('filter_grupo_id'))         ? $request->filter_grupo_id : null,
            'activo_f_tipo_id'          => ($request->has('filter_tipo_id'))          ? $request->filter_tipo_id : null,
            'activo_f_marca'            => ($request->has('filter_marca'))            ? $request->filter_marca : null,
            'activo_f_modelo'           => ($request->has('filter_modelo'))           ? $request->filter_modelo : null,
            'activo_f_orden_compra'     => ($request->has('filter_orden_compra'))     ? $request->filter_orden_compra : null,
            'activo_f_legajo'           => ($request->has('filter_legajo'))           ? $request->filter_legajo : null,
            'activo_f_cod_area'         => ($request->has('filter_cod_area'))         ? $request->filter_cod_area : null,
            'activo_f_ubicacion'        => ($request->has('filter_ubicacion'))        ? $request->filter_ubicacion : null,
            'activo_f_fecha_alta_desde' => ($request->has('filter_fecha_alta_desde')) ? $request->filter_fecha_alta_desde : null,
            'activo_f_fecha_alta_hasta' => ($request->has('filter_fecha_alta_hasta')) ? $request->filter_fecha_alta_hasta : null,
            'activo_f_habilitado'       => ($request->has('filter_habilitado'))       ? $request->filter_habilitado : null
        ]);

        // Fuerzo el guardado de la sesion (para que no tenga un 'salvado diferido' segun el driver)
        $request->session()->save();

        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Tipos de activos (paramétrico)
        $activo_tipos = ActivoTipo::orderBy('nombre')->get();

        // Grupos de tipos de activo (paramétrico)
        $grupos = Grupo::orderBy('nombre')->get();

        // Filtos activos
        $filtros = [];
        $criterios = [
            'grupo_id',
            'tipo_id',
            'marca',
            'modelo',
            'orden_compra',
            'legajo',
            'cod_area',
            'ubicacion',
            'fecha_alta_desde',
            'fecha_alta_hasta',
            'habilitado'
        ];

        foreach ($criterios as $c)
            if (request()->session()->has('activo_f_'.$c))
                $filtros['filter_'.$c] = request()->session()->get('activo_f_'.$c);

        // Llamo a la vista
        return view('activos.index', compact('activo_tipos', 'grupos', 'filtros'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $activo = new Activo();
        $activo->tipo_id = config('defaults.activoTipoID');
        $activo->fecha_alta = date('Y-m-d');
        $activo->marca = config('defaults.marca');
        $activo->modelo = config('defaults.modelo');
        $activo->tipo_origen = config('defaults.tipo_origen');
        $activo->titularidad = config('defaults.titularidad');
        $activo->estado = config('defaults.estado');
        $activo->condicion_uso = config('defaults.condicion_uso');
        $activo->habilitado = config('defaults.habilitado');

        $activo_tipos = ActivoTipo::orderBy('nombre')->get();

        return view('activos.edit', compact('activo', 'activo_tipos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ActivoSaveRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivoSaveRequest $request)
    {
        // Las validaciones estan en /app/Http/Requests/ActivoSaveRequest

        $activo = Activo::create([
            'tipo_id' => $request->tipo_id,
            'legajo' => $request->legajo,
            'user_id' => Auth::user()->id,
            'nro_inventario' => $request->nro_inventario,
            'orden_compra' => $request->orden_compra,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'nro_serie' => $request->nro_serie,
            'ubicacion' => $request->ubicacion,
            'fecha_alta' => $request->fecha_alta,
            'tipo_origen' => $request->tipo_origen,
            'titularidad' => $request->titularidad,
            'estado' => $request->estado,
            'condicion_uso' => $request->condicion_uso,
            'nombre_equipo' => $request->nombre_equipo,
            'sistema_operativo' => $request->sistema_operativo,
            'sistema_operativo_serie' => $request->sistema_operativo_serie,
            'cpu' => $request->cpu,
            'memoria' => $request->memoria,
            'motherboard' => $request->motherboard,
            'hd_marca' => $request->hd_marca,
            'hd_capacidad' => $request->hd_capacidad,
            'dvd_rw' => $request->dvd_rw,
            'dvd_rw_marca' => $request->dvd_rw_marca,
            'ethernet_dinamico' => $request->ethernet_dinamico,
            'ethernet_mac' => $request->ethernet_mac,
            'ethernet_ip' => $request->ethernet_ip ?? '',
            'ethernet_mask' => $request->ethernet_mask ?? '',
            'ethernet_gw' => $request->ethernet_gw ?? '',
            'ethernet_dns' => $request->ethernet_dns ?? '',
            'wireless_dinamico' => $request->wireless_dinamico,
            'wireless_mac' => $request->wireless_mac,
            'wireless_ip' => $request->wireless_ip ?? '',
            'wireless_mask' => $request->wireless_mask ?? '',
            'wireless_gw' => $request->wireless_gw ?? '',
            'wireless_dns' => $request->wireless_dns ?? '',
            'fuente' => $request->fuente,
            'observaciones' => $request->observaciones,
            'habilitado' => $request->habilitado
        ]);

        return redirect()->route('activos.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Activo  $activo
     * @return \Illuminate\Http\Response
     */
    public function show(Activo $activo)
    {
        $archivos = [];
        $directorio = public_path('activos_pdf');
        $id = $activo->id;

        if (File::exists($directorio)) {
            $archivos = File::files($directorio);
            $archivos = array_filter($archivos, function($archivo) use ($id) {
                return strpos($archivo->getFilename(), $id . '_') === 0;
            });
        }

        return view('activos.show', compact('activo', 'archivos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activo  $activo
     * @return \Illuminate\Http\Response
     */
    public function edit(Activo $activo)
    {
        $activo_tipos = ActivoTipo::orderBy('nombre')->get();

        $archivos = [];
        $directorio = public_path('activos_pdf');
        $id = $activo->id;

        if (File::exists($directorio)) {
            $archivos = File::files($directorio);
            $archivos = array_filter($archivos, function($archivo) use ($id) {
                return strpos($archivo->getFilename(), $id . '_') === 0;
            });
        }

        return view('activos.edit', compact('activo', 'activo_tipos', 'archivos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activo  $activo
     * @return \Illuminate\Http\Response
     */
    public function clone(Activo $activo_origen)
    {
        $activo = $activo_origen->replicate();
        $activo_tipos = ActivoTipo::orderBy('nombre')->get();

        return view('activos.edit', compact('activo', 'activo_tipos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ActivoSaveRequest  $request
     * @param  \App\Activo  $activo
     * @return \Illuminate\Http\Response
     */
    public function update(ActivoSaveRequest $request, Activo $activo)
    {
        // Las validaciones estan en /app/Http/Requests/ActivoSaveRequest

        // Nota: NO se almacenan los nombres de los pdf en la DB.
        // Se acceden a ellos usando el Id del Activo, ejemplo: '1315_'

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $originalName = $archivo->getClientOriginalName();

            $sanitizedOriginalName = str_replace('.pdf', '', $originalName);
            $sanitizedOriginalName = str_replace('.PDF', '', $sanitizedOriginalName);
            $sanitizedOriginalName = str_replace('~', '_', $sanitizedOriginalName);
            $sanitizedOriginalName = str_replace(' ', '_', $sanitizedOriginalName);

            $nombreArchivo = $activo->id . '_' . time() . '__'.$sanitizedOriginalName.'.pdf';

            $archivo->move(public_path('activos_pdf'), $nombreArchivo);
        }

        $activo->fill([
            'tipo_id' => $request->tipo_id,
            'legajo' => $request->legajo,
            'user_id' => Auth::user()->id,
            'nro_inventario' => $request->nro_inventario,
            'orden_compra' => $request->orden_compra,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'nro_serie' => $request->nro_serie,
            'ubicacion' => $request->ubicacion,
            'fecha_alta' => $request->fecha_alta,
            'tipo_origen' => $request->tipo_origen,
            'titularidad' => $request->titularidad,
            'estado' => $request->estado,
            'condicion_uso' => $request->condicion_uso,
            'nombre_equipo' => $request->nombre_equipo,
            'sistema_operativo' => $request->sistema_operativo,
            'sistema_operativo_serie' => $request->sistema_operativo_serie,
            'cpu' => $request->cpu,
            'memoria' => $request->memoria,
            'motherboard' => $request->motherboard,
            'hd_marca' => $request->hd_marca,
            'hd_capacidad' => $request->hd_capacidad,
            'dvd_rw' => $request->dvd_rw,
            'dvd_rw_marca' => $request->dvd_rw_marca,
            'ethernet_dinamico' => $request->ethernet_dinamico,
            'ethernet_mac' => $request->ethernet_mac,
            'ethernet_ip' => $request->ethernet_ip ?? '',
            'ethernet_mask' => $request->ethernet_mask ?? '',
            'ethernet_gw' => $request->ethernet_gw ?? '',
            'ethernet_dns' => $request->ethernet_dns ?? '',
            'wireless_dinamico' => $request->wireless_dinamico,
            'wireless_mac' => $request->wireless_mac,
            'wireless_ip' => $request->wireless_ip ?? '',
            'wireless_mask' => $request->wireless_mask ?? '',
            'wireless_gw' => $request->wireless_gw ?? '',
            'wireless_dns' => $request->wireless_dns ?? '',
            'fuente' => $request->fuente,
            'observaciones' => $request->observaciones,
            'habilitado' => $request->habilitado
        ]);

        $activo->save();

        return redirect()->route('activos.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activo  $activo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activo $activo)
    {
        $activo->delete();

        return redirect()->route('activos.index');
    }

    /**
     * [jsonGetById description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function jsonGetById($id)
    {
        $jsonData = [
            'status' => 'OK',
            'message' => '',
            'data' => null
        ];
        if ($activo = Activo::find($id)) {
            $jsonData['message'] = sprintf('Activo %s obtenido con éxito.', $id);
            $jsonData['data'] = $activo;
        } else {
            $jsonData['status'] = 'ERROR';
            $jsonData['message'] = sprintf('Activo %s inexistente.', $id);
        }

        return response()->json($jsonData);
    }

    /**
     * [jsonVerifyByNroInventario description]
     * @param  [type] $id             [description]
     * @param  [type] $nro_inventario [description]
     * @return [type]                 [description]
     */
    public function jsonVerifyByNroInventario()
    {
        /*
        $validator = Validator::make(request()->all(), [
            'id' => 'present|numeric|min:1',
            'nro_inventario' => 'present|string|max:20'
        ]);

        if ($validator->fails()) {
            return ['status' => 'ERROR', 'message' => $validator->messages(), 'data' => null];
        }
        */

        $id = request()->input('id');
        $nro_inventario = request()->input('nro_inventario');

        // Si el ID existe en la base de datos, es un Activo editado...
        if ($activo = Activo::find($id)) {
            $cant_duplicados = Activo::whereNotNull('nro_inventario')
                ->where('nro_inventario', '!=', '')
                ->where('nro_inventario', $nro_inventario)
                ->get()
                ->count();

            if ($cant_duplicados > 0) {
                // mas de uno? esta duplicado
                if ($cant_duplicados > 1)
                    $jsonData = ['status' => 'WARNING', 'message' => 'Ya existen activos con el mismo Nro. de Registro Patrimonial.', 'data' => null];
                else {
                    // hay uno solo... si no es él mismo, entonces hay duplicado
                    if ($activo->nro_inventario != $nro_inventario) {
                        $jsonData = ['status' => 'WARNING', 'message' => 'Ya existen activos con el mismo Nro. de Registro Patrimonial.', 'data' => null];
                    } else {
                        // es él mismo? entonces no lo contamos como duplicado
                        $jsonData = ['status' => 'OK', 'message' => 'No existen duplicados para el activo.', 'data' => null];
                    }
                }
            } else {
                $jsonData = ['status' => 'OK', 'message' => 'No existen duplicados para el activo.', 'data' => null];
            }

        } else {
            // Si el ID NO existe en la base de datos, es un Activo nuevo...
            $cant_duplicados = Activo::whereNotNull('nro_inventario')
                ->where('nro_inventario', '!=', '')
                ->where('nro_inventario', $nro_inventario)
                ->get()
                ->count();

            if ($cant_duplicados > 0) {
                $jsonData = ['status' => 'WARNING', 'message' => 'Ya existen activos con el mismo Nro. de Registro Patrimonial.', 'data' => null];
            } else {
                $jsonData = ['status' => 'OK', 'message' => 'No existen duplicados para el activo.', 'data' => null];
            }
        }

        return response()->json($jsonData);
    }

    /**
     * [getDatatablesJson description]
     * @param  [type] $event_id [description]
     * @return [type]           [description]
     */
    public function getDatatablesJson(Request $request)
    {
        $data = DB::table('activos')
            ->join('activo_tipos', 'activos.tipo_id', '=', 'activo_tipos.id')
            ->join('activo_tipos_grupos', 'activo_tipos.id', '=', 'activo_tipos_grupos.tipo_id')
            ->join('responsables', 'activos.legajo', '=', 'responsables.legajo')
            ->join('areas', 'responsables.cod_area', '=', 'areas.cod_area')
            ->select(
                // Se usa el 'as' con el mismo nombre para que se preserve el nombre de la tabla
                // y no haya conflicto con los campos en el DataTables del lado del cliente.
                'activos.id as activos.id',
                'activos.nro_inventario as activos.nro_inventario',
                'activos.marca as activos.marca',
                'activos.modelo as activos.modelo',
                'activos.orden_compra as activos.orden_compra',
                'activos.ubicacion as activos.ubicacion',
                'activos.nombre_equipo as activos.nombre_equipo',
                'activos.nro_serie as activos.nro_serie',
                'activos.tipo_origen as activos.tipo_origen',
                'activos.estado as activos.estado',
                'activos.condicion_uso as activos.condicion_uso',
                'activos.fecha_alta as activos.fecha_alta',
                'activos.habilitado as activos.habilitado',
                'activo_tipos.nombre as activo_tipos.nombre',
                'responsables.legajo as responsables.legajo',
                'responsables.apellido as responsables.apellido',
                'responsables.nombre as responsables.nombre',
                'areas.cod_area as areas.cod_area',
                'areas.nombre as areas.nombre'
            )
            ->whereNull('activos.deleted_at');

        // Agregar filtros customizados
        $ef = [];
        if ($request->has('filter_grupo_id'))     $ef[] = ['activo_tipos_grupos.grupo_id', '=', $request->filter_grupo_id];
        if ($request->has('filter_tipo_id'))      $ef[] = ['activos.tipo_id', '=', $request->filter_tipo_id];
        if ($request->has('filter_marca'))        $ef[] = ['activos.marca', 'LIKE', '%'.$request->filter_marca.'%'];
        if ($request->has('filter_modelo'))       $ef[] = ['activos.modelo', 'LIKE', '%'.$request->filter_modelo.'%'];
        if ($request->has('filter_orden_compra')) $ef[] = ['activos.orden_compra', '=', $request->filter_orden_compra];
        if ($request->has('filter_legajo'))       $ef[] = ['activos.legajo', '=', $request->filter_legajo];
        if ($request->has('filter_cod_area'))     $ef[] = ['responsables.cod_area', '=', $request->filter_cod_area];
        if ($request->has('filter_ubicacion'))    $ef[] = ['activos.ubicacion', 'LIKE', '%'.$request->filter_ubicacion.'%'];

        if ($request->has('filter_fecha_alta_desde')) $ef[] = ['activos.fecha_alta', '>=', $request->filter_fecha_alta_desde];
        if ($request->has('filter_fecha_alta_hasta')) $ef[] = ['activos.fecha_alta', '<=', $request->filter_fecha_alta_hasta];

        if ($request->has('filter_habilitado'))   $ef[] = ['activos.habilitado', '=', $request->filter_habilitado];

        if (count($ef) > 0)
            $data->where($ef);

        // Guardo los filtros seleccionados
        // El resto de la configuracion del datatables se guarda por si sola, segun parametro stateSave
        $this->saveIndexParams($request);

        // Devolver datos
        return Datatables::of($data)->make(true);
    }

    /**
     * [getAutocompleteMarcaJson description]
     * @return [type] [description]
     */
    public function getAutocompleteMarcaJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('marca')
                ->whereNotNull('marca')
                ->where('marca', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('marca', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        if (is_null($data->firstWhere('marca', config('defaults.marca'))))
            $data->push(['marca' => config('defaults.marca')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteModeloJson description]
     * @return [type] [description]
     */
    public function getAutocompleteModeloJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('modelo')
                ->whereNotNull('modelo')
                ->where('modelo', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('modelo', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        if (is_null($data->firstWhere('modelo', config('defaults.modelo'))))
            $data->push(['modelo' => config('defaults.modelo')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteOrdenCompraJson description]
     * @return [type] [description]
     */
    public function getAutocompleteOrdenCompraJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('orden_compra')
                ->whereNotNull('orden_compra')
                ->where('orden_compra', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('orden_compra', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        return response()->json($data);
    }

    /**
     * [getAutocompleteSistemaOperativoJson description]
     * @return [type] [description]
     */
    public function getAutocompleteSistemaOperativoJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('sistema_operativo')
                ->whereNotNull('sistema_operativo')
                ->where('sistema_operativo', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('sistema_operativo', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        if (is_null($data->firstWhere('sistema_operativo', config('defaults.sistemaOperativo'))))
            $data->push(['sistema_operativo' => config('defaults.sistemaOperativo')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteCpuJson description]
     * @return [type] [description]
     */
    public function getAutocompleteCpuJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('cpu')
                ->whereNotNull('cpu')
                ->where('cpu', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('cpu', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        // if (is_null($data->firstWhere('cpu', config('defaults.marca'))))
        //     $data->push(['cpu' => config('defaults.marca')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteMotherboardJson description]
     * @return [type] [description]
     */
    public function getAutocompleteMotherboardJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('motherboard')
                ->whereNotNull('motherboard')
                ->where('motherboard', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('motherboard', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        // if (is_null($data->firstWhere('motherboard', config('defaults.marca'))))
        //     $data->push(['motherboard' => config('defaults.marca')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteHdMarcaJson description]
     * @return [type] [description]
     */
    public function getAutocompleteHdMarcaJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('hd_marca')
                ->whereNotNull('hd_marca')
                ->where('hd_marca', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('hd_marca', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        // if (is_null($data->firstWhere('hd_marca', config('defaults.marca'))))
        //     $data->push(['hd_marca' => config('defaults.marca')]);

        return response()->json($data);
    }

    /**
     * [getAutocompleteDvdRwMarcaJson description]
     * @return [type] [description]
     */
    public function getAutocompleteDvdRwMarcaJson(Request $request) {
        // Es poco performante (se trae todos los resultados para una busqueda "vacia")... pero es a pedido del jefe.
        $data = DB::table('activos')
                ->select('dvd_rw_marca')
                ->whereNotNull('dvd_rw_marca')
                ->where('dvd_rw_marca', '<>', "");

        if ($request->has('q')) {
            $search = $request->q;
            $data = $data->where('dvd_rw_marca', 'LIKE', "%$search%");
        }

        // Ejecuto la consulta
        $data = $data->distinct()->get();

        // Agrego valor por defecto, si no existe
        // if (is_null($data->firstWhere('dvd_rw_marca', config('defaults.marca'))))
        //     $data->push(['dvd_rw_marca' => config('defaults.marca')]);

        return response()->json($data);
    }

    /**
     * [exportPDF description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function exportPDF(Request $request) {

        $request->validate([
            'sort_col' => 'required|in:activos_id,activos_nro_inventario,activos_marca,activos_modelo',
            'sort_type' => 'required|in:sort_asc,sort_desc',
            'filter_grupo_id' => 'sometimes|integer|min:1|exists:grupos,id',
            'filter_tipo_id' => 'sometimes|integer|min:1|exists:activo_tipos,id',
            'filter_marca' => 'sometimes|string|max:100',
            'filter_modelo' => 'sometimes|string|max:100',
            'filter_orden_compra' => 'sometimes|string|max:50',
            'filter_fecha_alta_desde' => 'sometimes|date_format:"Y-m-d"', // validar
            'filter_fecha_alta_hasta' => 'sometimes|date_format:"Y-m-d"', // validar
            'filter_legajo' => 'sometimes|integer|min:0|exists:responsables,legajo',
            'filter_cod_area' => 'sometimes|string|max:8|exists:areas,cod_area',
            'filter_ubicacion' => 'sometimes|string|max:100'
        ]);

        // Genero un select dinamico (los campos que requiero para el query)
        // Se hace así para no tener que tocar este query cada vez que se modifica la clase "activos"
        $atributos_activo = [];
        foreach (\Illuminate\Support\Facades\Schema::getColumnListing('activos') as $v) {
            $atributos_activo[] = sprintf('activos.%1$s as `activos.%1$s`', $v);
        }

        $query = DB::table('activos')
            ->join('activo_tipos', 'activos.tipo_id', '=', 'activo_tipos.id')
            ->join('activo_tipos_grupos', 'activo_tipos.id', '=', 'activo_tipos_grupos.tipo_id')
            ->join('responsables', 'activos.legajo', '=', 'responsables.legajo')
            ->join('areas', 'responsables.cod_area', '=', 'areas.cod_area')
            ->select(
                // Se usa el 'as' con el mismo nombre para que se preserve el nombre de la tabla
                // y no haya conflicto con los campos en el DataTables del lado del cliente.
                DB::raw(implode(',', $atributos_activo)),
                'activo_tipos.nombre as activo_tipos.nombre',
                'responsables.legajo as responsables.legajo',
                'responsables.apellido as responsables.apellido',
                'responsables.nombre as responsables.nombre',
                'areas.cod_area as areas.cod_area',
                'areas.nombre as areas.nombre'
            )
            ->whereNull('activos.deleted_at');

        // Agregar filtros customizados
        $ef = [];
        if ($request->has('filter_grupo_id'))     $ef[] = ['activo_tipos_grupos.grupo_id', '=', $request->filter_grupo_id];
        if ($request->has('filter_tipo_id'))      $ef[] = ['activos.tipo_id', '=', $request->filter_tipo_id];
        if ($request->has('filter_marca'))        $ef[] = ['activos.marca', 'LIKE', '%'.$request->filter_marca.'%'];
        if ($request->has('filter_modelo'))       $ef[] = ['activos.modelo', 'LIKE', '%'.$request->filter_modelo.'%'];
        if ($request->has('filter_orden_compra')) $ef[] = ['activos.orden_compra', '=', $request->filter_orden_compra];
        if ($request->has('filter_legajo'))       $ef[] = ['activos.legajo', '=', $request->filter_legajo];
        if ($request->has('filter_cod_area'))     $ef[] = ['responsables.cod_area', '=', $request->filter_cod_area];
        if ($request->has('filter_ubicacion'))    $ef[] = ['activos.ubicacion', 'LIKE', '%'.$request->filter_ubicacion.'%'];

        if ($request->has('filter_fecha_alta_desde')) $ef[] = ['activos.fecha_alta', '>=', $request->filter_fecha_alta_desde];
        if ($request->has('filter_fecha_alta_hasta')) $ef[] = ['activos.fecha_alta', '<=', $request->filter_fecha_alta_hasta];

        if ($request->has('filter_habilitado'))   $ef[] = ['activos.habilitado', '=', $request->filter_habilitado];

        if (count($ef) > 0)
            $query->where($ef);

        // Agregar criterio de orden
        $orderCriteria = [
            'activos_id' => 'activos.id',
            'activos_nro_inventario' => 'activos.nro_inventario',
            'activos_marca' => 'activos.marca',
            'activos_modelo' => 'activos.modelo'
        ];
        $orderTypeCriteria = [
            'sort_asc' => 'asc',
            'sort_desc' => 'desc'
        ];
        $query->orderBy($orderCriteria[$request->sort_col], $orderTypeCriteria[$request->sort_type]);

        // Ejecutar consulta
        $activos = $query->get();

        // Mapeo campos faltantes
        $activos->each(function($item) {
            $item->tipo_origen_desc = FakeAttrib::tipoOrigenDesc($item->{'activos.tipo_origen'});
            $item->estado_desc = FakeAttrib::estadoDesc($item->{'activos.estado'});
            $item->titularidad_desc = FakeAttrib::titularidadDesc($item->{'activos.titularidad'});
            $item->condicion_uso_desc = FakeAttrib::condicionUsoDesc($item->{'activos.condicion_uso'});
        });

        // Parametros de PHP para la generación del reporte
        // (Esto aplica solamente para esta llamada del método del controlador; despues vuelve a la normalidad)
        ini_set('memory_limit', '512M');      // Extiende la cantidad de memoria disponible.
        ini_set('max_execution_time', '300'); // Extiende el tiempo de ejecución a 5 minutos.

        // Generar PDF
        $pdf = PDF::loadView('activos.reportpdf', compact('activos'));

        return $pdf
            ->setPaper('a4', 'landscape')
            ->download('activos.pdf');
    }

    /**
     * [tomaInventario description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function tomaInventario(Request $request) {

        $request->validate([
            'secretaria' => 'required|string|min:1',
            'dependencia' => 'required|string|min:1',
            'sort_col' => 'required|in:activos_id,activos_nro_inventario,activos_marca,activos_modelo',
            'sort_type' => 'required|in:sort_asc,sort_desc',
            'filter_grupo_id' => 'sometimes|integer|min:1|exists:grupos,id',
            'filter_tipo_id' => 'sometimes|integer|min:1|exists:activo_tipos,id',
            'filter_marca' => 'sometimes|string|max:100',
            'filter_modelo' => 'sometimes|string|max:100',
            'filter_orden_compra' => 'sometimes|string|max:50',
            'filter_fecha_alta_desde' => 'sometimes|date_format:"Y-m-d"', // validar
            'filter_fecha_alta_hasta' => 'sometimes|date_format:"Y-m-d"', // validar
            'filter_legajo' => 'sometimes|integer|min:0|exists:responsables,legajo',
            'filter_cod_area' => 'sometimes|string|max:8|exists:areas,cod_area',
            'filter_ubicacion' => 'sometimes|string|max:100'
        ]);

        // Genero un select dinamico (los campos que requiero para el query)
        // Se hace así para no tener que tocar este query cada vez que se modifica la clase "activos"
        $atributos_activo = [];
        foreach (\Illuminate\Support\Facades\Schema::getColumnListing('activos') as $v) {
            $atributos_activo[] = sprintf('activos.%1$s as %1$s', $v);
        }

        $query = DB::table('activos')
            ->join('activo_tipos', 'activos.tipo_id', '=', 'activo_tipos.id')
            ->join('activo_tipos_grupos', 'activo_tipos.id', '=', 'activo_tipos_grupos.tipo_id')
            ->select(
                DB::raw(implode(',', $atributos_activo)),
                'activo_tipos.nombre as nombre',
                'activo_tipos.atrib_desc_dinamica as atrib_desc_dinamica'
            )
            ->whereNull('activos.deleted_at');

        // Agregar filtros customizados
        $ef = [];
        if ($request->has('filter_grupo_id'))     $ef[] = ['activo_tipos_grupos.grupo_id', '=', $request->filter_grupo_id];
        if ($request->has('filter_tipo_id'))      $ef[] = ['activos.tipo_id', '=', $request->filter_tipo_id];
        if ($request->has('filter_marca'))        $ef[] = ['activos.marca', 'LIKE', '%'.$request->filter_marca.'%'];
        if ($request->has('filter_modelo'))       $ef[] = ['activos.modelo', 'LIKE', '%'.$request->filter_modelo.'%'];
        if ($request->has('filter_orden_compra')) $ef[] = ['activos.orden_compra', '=', $request->filter_orden_compra];
        if ($request->has('filter_legajo'))       $ef[] = ['activos.legajo', '=', $request->filter_legajo];
        if ($request->has('filter_cod_area'))     $ef[] = ['responsables.cod_area', '=', $request->filter_cod_area];
        if ($request->has('filter_ubicacion'))    $ef[] = ['activos.ubicacion', 'LIKE', '%'.$request->filter_ubicacion.'%'];

        if ($request->has('filter_fecha_alta_desde')) $ef[] = ['activos.fecha_alta', '>=', $request->filter_fecha_alta_desde];
        if ($request->has('filter_fecha_alta_hasta')) $ef[] = ['activos.fecha_alta', '<=', $request->filter_fecha_alta_hasta];

        if ($request->has('filter_habilitado'))   $ef[] = ['activos.habilitado', '=', $request->filter_habilitado];

        if (count($ef) > 0)
            $query->where($ef);

        // Agregar criterio de orden
        $orderCriteria = [
            'activos_id' => 'activos.id',
            'activos_nro_inventario' => 'activos.nro_inventario',
            'activos_marca' => 'activos.marca',
            'activos_modelo' => 'activos.modelo'
        ];
        $orderTypeCriteria = [
            'sort_asc' => 'asc',
            'sort_desc' => 'desc'
        ];
        $query->orderBy($orderCriteria[$request->sort_col], $orderTypeCriteria[$request->sort_type]);

        // Ejecutar consulta
        $activos = $query->get();

        // Mapeo campos faltantes
        $activos->each(function($item) {
            $item->tipo_origen_desc = FakeAttrib::tipoOrigenDesc($item->tipo_origen);
            $item->estado_desc = FakeAttrib::estadoDesc($item->estado);
            $item->titularidad_desc = FakeAttrib::titularidadDesc($item->titularidad);
            $item->condicion_uso_desc = FakeAttrib::condicionUsoDesc($item->condicion_uso);
            $item->desc_dinamica = FakeAttrib::descDinamica($item, $item->atrib_desc_dinamica);
        });

        // Parametros de PHP para la generación del reporte
        // (Esto aplica solamente para esta llamada del método del controlador; despues vuelve a la normalidad)
        ini_set('memory_limit', '512M');      // Extiende la cantidad de memoria disponible.
        ini_set('max_execution_time', '300'); // Extiende el tiempo de ejecución a 5 minutos.

        // Parametros extra
        $secretaria = $request->secretaria;
        $dependencia = $request->dependencia;

        // Generar PDF (se hace de esta forma para poder numerar las páginas)
        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('activos.tomainventariopdf', compact('activos','secretaria','dependencia'));

        return $pdf
            ->setPaper('legal', 'landscape')
            ->download('tomainventario.pdf');
    }

    /**
     * Ficha del Activo en PDF (13/11/2019 XXXX)
     * @param  Activo $activo [description]
     * @return [type]         [description]
     */
    public function fichaPDF(Activo $activo) {

        // Se genera la Ficha en formato PDF a partir de la vista de la ficha
        $pdf = PDF::loadView('activos.fichapdf', compact('activo'));

        $nombre_tipo_activo = mb_strtolower(str_replace(' ', '_', $activo->activo_tipo->nombre));

        return $pdf
            ->setPaper('a4')
            ->download("ficha_activo_$nombre_tipo_activo.pdf");
    }

    /**
     * Etiqueta del Activo en PDF (20/11/2019 XXXX)
     * @param  Activo $activo [description]
     * @return [type]         [description]
     */
    public function etiquetaPdf(Activo $activo) {

        // Se genera la Etiqueta en formato PDF a partir de la vista de la ficha
        $pdf = PDF::loadView('activos.etiquetapdf', compact('activo'));

        $nombre_tipo_activo = mb_strtolower(str_replace(' ', '_', $activo->activo_tipo->nombre));

        return $pdf
            ->setPaper('a4')
            ->download("etiqueta_activo_$nombre_tipo_activo.pdf");
    }

    /**
     * Se elimina un archivo adjunto del Activo
     * @param  string $filename Nombre del archivo
     */
    public function deleteFile($filename)
    {
        $filePath = public_path('activos_pdf') . '/' . $filename;

        if (File::exists($filePath)) {
            File::delete($filePath);
            return back()->with('success', 'Archivo eliminado exitosamente.');
        } else {
            return back()->with('error', 'El archivo no existe.');
        }
    }
}
