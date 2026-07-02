<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Area;
use App\Responsable;

class ImportDataController extends Controller
{
    /**
     * [importSGLData description]
     * @return [type] [description]
     */
    public function importSGLData(Request $request)
    {
        // Esta API solamente puede ser invocada localmente
        if (! in_array(request()->ip(), config('defaults.allowedImportSourceIP'))) 
            return response()->json(['status' => 'ERROR', 'message' => sprintf('Acceso restringido a la API para este cliente (client ip: %s).', request()->ip()), 'data' => null]);            

        // Verifico tener los archivo necesarios
        if (! Storage::disk('public')->exists('inventario_data.flag')) 
            return response()->json(['status' => 'WARNING', 'message' => 'Marca de procesamiento no encontrada. No hay datos para procesar.', 'data' => null]);

        if (! Storage::disk('public')->exists('inventario_data.json.md5')) 
            return response()->json(['status' => 'ERROR', 'message' => 'No se encuentra archivo de suma de verificación.', 'data' => null]);

        if (! Storage::disk('public')->exists('inventario_data.json')) 
            return response()->json(['status' => 'ERROR', 'message' => 'No se encuentra archivo de datos.', 'data' => null]);
        
        // Obtengo la suma de verificacion
        $md5_sum = file_get_contents(Storage::disk('public')->path('inventario_data.json.md5'));

        // Verifico si la suma de verificacion es correcta
        if (md5_file(Storage::disk('public')->path('inventario_data.json')) != $md5_sum)
            return response()->json(['status' => 'ERROR', 'message' => 'La suma de verificación no coincide.', 'data' => null]);

        // Leo el archivo json para generar la estructura a importar, y verifico su estructura
        $data = json_decode(file_get_contents(Storage::disk('public')->path('inventario_data.json')), true);
        if (is_null($data))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos a importar no es una estructura json válida.', 'data' => null]);
        if (! array_key_exists('status', $data))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos no posee una marca de estado.', 'data' => null]);
        if (strtoupper($data['status']) != 'OK')
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos no posee una marca de estado permitida para procesamiento.', 'data' => null]);
        
        if (! array_key_exists('data', $data))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos no posee un bloque de datos.', 'data' => null]);
        if (is_null($data['data']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos posee un bloque de datos nulo.', 'data' => null]);
        
        if (! array_key_exists('personal', $data['data']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos no posee un bloque con datos de personal.', 'data' => null]);
        if (is_null($data['data']['personal']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos posee un bloque de datos de personal nulo.', 'data' => null]);
        if (! is_array($data['data']['personal']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos posee un bloque de datos de personal inválido.', 'data' => null]);
        
        if (! array_key_exists('areas', $data['data']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos no posee un bloque con datos de areas.', 'data' => null]);
        if (is_null($data['data']['areas']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos posee un bloque de datos de areas nulo.', 'data' => null]);
        if (! is_array($data['data']['areas']))
            return response()->json(['status' => 'ERROR', 'message' => 'El lote de datos posee un bloque de datos de areas inválido.', 'data' => null]);

        // ---- Areas
        // Actualizo datos y contabilizo resultados
        $areas_proc = 0;
        $areas_new = 0;
        $areas_upd = 0;
        $areas_del = 0;
        foreach ($data['data']['areas'] as $v) {
            $area = Area::updateOrCreate(
                ['cod_area' => $v['ca_id']],
                [
                    'nombre' => $v['ca_nombre'],
                    'tipo' => $v['ca_tipo'],
                    'cod_area_padre' => $v['ca_depende_de']
                ]);
            if ($area->wasRecentlyCreated) $areas_new++; else $areas_upd++;
            $areas_proc++;
        }

        // ---- Personal
        // Actualizo datos y contabilizo resultados
        $personal_proc = 0;
        $personal_new = 0;
        $personal_upd = 0;
        foreach ($data['data']['personal'] as $v) {
            $responsable = Responsable::updateOrCreate(
                ['legajo' => $v['p_legajo']],
                [
                    'apellido' => $v['p_apellido'],
                    'nombre' => $v['p_nombre'],
                    'tipo_doc' => $v['p_tipo_documento'],
                    'nro_doc' => $v['p_nro_documento'],
                    'tel_fijo' => $v['p_telefono'],
                    'tel_movil' => $v['p_celular'],
                    'email' => '',
                    'domicilio' => $v['p_domicilio'],
                    'observaciones' => '',
                    'cod_area' => $v['id_area']
                ]);
            if ($responsable->wasRecentlyCreated) $personal_new++; else $personal_upd++;
            $personal_proc++;
        }

        // ---- Elimino las areas que no tienen responsables asignados 
        // y que no son padre de ninguna otra area
        $areas_sin_responsable = Area::doesntHave('responsables')->get();
        foreach ($areas_sin_responsable as $a) {
            if (Area::where('cod_area_padre', $a->cod_area)->count() == 0) {
                $a->delete();
                $areas_del++;
            }
        }

        // ---- Limpio los archivos de migración
        $remove_ok = true;
        try {
            $remove_ok = $remove_ok && unlink(Storage::disk('public')->path('inventario_data.flag'));
            $remove_ok = $remove_ok && unlink(Storage::disk('public')->path('inventario_data.json.md5'));
            $remove_ok = $remove_ok && unlink(Storage::disk('public')->path('inventario_data.json'));
        } catch (Exception $e) {
            $remove_ok = false;        
        }

        // ---- Return data
        return response()->json([
            'status' => ($remove_ok) ? 'OK' : 'WARNING', 
            'message' => ($remove_ok) ? 'Migración exitosa.' : 'La migración fue exitosa, pero no han podido eliminarse los archivos de datos; por favor verifique permisos.', 
            'data' => [
                'cant_areas' => count($data['data']['areas']),
                'cant_areas_proc' => $areas_proc,
                'cant_areas_new' => $areas_new,
                'cant_areas_upd' => $areas_upd,
                'cant_areas_del' => $areas_del,
                'cant_personal' => count($data['data']['personal']),
                'cant_personal_proc' => $personal_proc,
                'cant_personal_new' => $personal_new,
                'cant_personal_upd' => $personal_upd
            ]
        ]);
    }
}
