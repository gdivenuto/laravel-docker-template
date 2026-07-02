<?php
namespace App\Helpers;

/**
 * FakeAttrib - Helper
 *
 * Esta clase se utiliza para extraer la implementación de la manipulación
 * de determinados atributos "falsos" (Accessors) de algunos Modelos.
 * Esto se hace para poder aplicar la misma lógica a cualquier dato obtenido
 * de la DB utilizando queries de tipo "raw", donde obviamente no se dispone
 * del Modelo.
 */

use Illuminate\Support\Str;
use DB;

class FakeAttrib {

    /**
     * [basesList description]
     * @return [type] [description]
     */
    public static function basesList()
    {
        return [
            'normas'  => 'Normas',
            'sesiones' => 'Sesiones',
            'decretos' => 'Decretos'
        ];
    }

    /**
     * [tipoActoList description]
     * @return [type] [description]
     */
    public static function tipoActoList($base = 'todas')
    {
        $lista = [];

        switch ($base) {
            case 'normas':   
                $lista = [
                    'ac' => 'ACORDADA',
                    'de' => 'DECRETO D.E.',
                    'do' => 'DECRETO ORDENANZA',
                    'le' => 'L E Y',
                    'og' => 'ORDENANZA GENERAL PROVINCIA',
                    'or' => 'ORDENANZA MUNICIPAL',
                    're_nor' => 'RESOLUCIÓN',
                ]; 
                
                break;
            
            case 'sesiones': 
                $lista = [
                    '' => 'SIN ACTO',
                    'ap' => 'AUDIENCIA PÚBLICA',
                    'ba' => 'BANCA ABIERTA',
                    'co' => 'COMUNICACIÓN HCD',
                    'cp' => 'CUESTIÓN PREVIA',
                    'da' => 'DIALOGOS ARGENTINOS',
                    'de_ses' => 'DECRETO HCD',
                    'dp' => 'DECRETO PRESIDENCIA HCD',
                    'jt' => 'JORNADA TRABAJO',
                    're_ses' => 'RESOLUCIÓN HCD',
                ];
                
                break;

            case 'decretos':
                $lista = [
                    'de_dec' => 'DECRETO D.E.',
                    're' => 'RESOLUCIÓN',
                ];
                
                break;

           default:
                $lista = [
                    '' => 'SIN ACTO',
                    'ac' => 'ACORDADA',
                    'ap' => 'AUDIENCIA PÚBLICA',
                    'ba' => 'BANCA ABIERTA',
                    'co' => 'COMUNICACIÓN HCD',
                    'cp' => 'CUESTIÓN PREVIA',
                    'da' => 'DIALOGOS ARGENTINOS',
                    'de' => 'DECRETO D.E.',
                    'de_dec' => 'DECRETO D.E.',
                    'de_ses' => 'DECRETO HCD',
                    'do' => 'DECRETO ORDENANZA',
                    'dp' => 'DECRETO PRESIDENCIA HCD',
                    'jt' => 'JORNADA TRABAJO',
                    'le' => 'L E Y',
                    'og' => 'ORDENANZA GENERAL PROVINCIA',
                    'or' => 'ORDENANZA MUNICIPAL',
                    're' => 'RESOLUCIÓN',
                    're_nor' => 'RESOLUCIÓN',
                    're_ses' => 'RESOLUCIÓN HCD',
                ];

                break;
        }

        return $lista;
    }

    /**
     * [tipoActoRegEx description]
     * @return [type] [description]
     */
    public static function tipoActoRegEx()
    {
        return sprintf("^(%s)$", implode('|', array_keys(self::tipoActoList())));
    }

    /**
     * [tipoActoDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function tipoActoDesc($value)
    {
        return (array_key_exists(Str::lower($value), self::tipoActoList()))
            ? self::tipoActoList()[Str::lower($value)]
            : Str::upper($value);
    }

    /**
     * [tipoActoBase description]
     * @param  string $base [description]
     * @return [type]       [description]
     */
    public static function tipoActoBase($base = 'todas')
    {
        $data = [
            'co' =>     ['base' => 'sesiones', 'year' => false, 'desc' => 'COMUNICACIÓN HCD'],
            'de_dec' => ['base' => 'decretos', 'year' => true, 'desc' => 'DECRETO D.E.'],
            'de_ses' => ['base' => 'sesiones', 'year' => false, 'desc' => 'DECRETO HCD'],
            'do' =>     ['base' => 'normas', 'year' => true, 'desc' => 'DECRETO ORDENANZA'],
            'dp' =>     ['base' => 'sesiones', 'year' => true, 'desc' => 'DECRETO PRESIDENCIA HCD'],
            'le' =>     ['base' => 'normas', 'year' => false, 'desc' => 'L E Y'],
            'og' =>     ['base' => 'normas', 'year' => false, 'desc' => 'ORDENANZA GENERAL PROVINCIA'],
            'or' =>     ['base' => 'normas', 'year' => false, 'desc' => 'ORDENANZA MUNICIPAL'],
            're_nor' => ['base' => 'normas', 'year' => true, 'desc' => 'RESOLUCIÓN'],
            're_ses' => ['base' => 'sesiones', 'year' => false, 'desc' => 'RESOLUCIÓN HCD']
        ];

        return ($base == 'todas')
            ? $data
            : array_filter($data, function ($i) use ($base) {
                return $i['base'] == $base;
            });
    }

    /**
     * [tipoActoBaseDinamico description]
     * @param  string $base [description]
     * @return [type]       [description]
     */
    public static function tipoActoBaseDinamico($base = 'todas')
    {
        if ($base == 'todas') 
            return collect(self::tipoActoBase('todas'))
                ->mapWithKeys(function ($i, $k) {
                    return [$k => $i['desc']];
                })
                ->toArray();

        $tipos_validos = self::tipoActoList();

        // TODO: hay que arreglar esto para no hacer un barrido de la tabla!!!
        return DB::table('normas')
            ->select('acto')
            ->where('base', $base)
            ->distinct()
            ->orderBy('acto')
            ->get()
            ->filter(function ($v, $k) use ($tipos_validos) {
                return array_key_exists($v->acto, $tipos_validos);
            })
            ->mapWithKeys(function ($i) use ($tipos_validos) {
                return [$i->acto => $tipos_validos[$i->acto]];
            })
            ->toArray();
    }    

    /**
     * [relacionDesc description]
     * @param  [type] $value_s [description]
     * @param  [type] $value_t [description]
     * @return [type]          [description]
     */
    public static function relacionDesc($value_s, $value_t)
    {
        $resultado = '';
        if ($value_s == 'O') {
            switch ($value_t) {
                case 'A': $resultado = "Abroga"; break;
                case 'D': $resultado = "Deroga"; break;
                case 'R': $resultado = "Reglamenta"; break;
                case 'M': $resultado = "Modifica"; break;
            }
        } else { // $value_s == 'D'
            switch ($value_t) {
                case 'A': $resultado = "Abrogada por"; break;
                case 'D': $resultado = "Derogada por"; break;
                case 'R': $resultado = "Reglamentada por"; break;
                case 'M': $resultado = "Modificada por"; break;
            }
        }
        return $resultado;
    }

    /**
     * [alcanceDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function alcanceDesc($value)
    {
        return (Str::lower($value) == 'g') ? 'General' : 'Particular';
    }

    /**
     * [caracterDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function caracterDesc($value)
    {
        return (Str::lower($value) == 'p') ? 'Permanente' : 'Transitorio';
    }

    /**
     * [aprobadoDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function aprobadoDesc($value)
    {
        $resultado = '';
        switch (Str::lower($value)) {
            case 'u': $resultado = 'Por unanimidad'; break;
            case 'm': $resultado = 'Por mayoría'; break;
            default:  $resultado = '?';
        }
        return $resultado;
    }

    /**
     * [snDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function snDesc($value)
    {
        return (Str::lower($value) == 's') ? 'Sí' : 'No';
    }

    /**
     * [urlDoc description]
     * @param  [type]  $base           [description]
     * @param  [type]  $value_a        [description]
     * @param  [type]  $value_n        [description]
     * @param  [type]  $value_nh       [description]
     * @param  string  $value_ext      [description]
     * @param  boolean $is_actualizado [description]
     * @return [type]                  [description]
     */
    public static function urlDoc($base, $value_a, $value_n, $value_nh, $value_ext = 'html', $is_actualizado = false)
    {
        $base_dir = '';
        $ret_url = '';
        $actualizado = ($is_actualizado) ? 'a' : '';
        $value_a = strtolower($value_a);
        $value_n = strtolower($value_n);
        $value_nh = strtolower($value_nh);

        switch ($base) {
            case 'normas':   
                $base_dir = 'docs/'; 
                $ret_url = IntranetRsc::forceHttps(config('params.docBaseUrl')) . $base_dir . Str::substr($value_a, 0, 1) . $value_n . $actualizado . '.' . $value_ext;
                break;
            
            case 'sesiones': 
                $base_dir = 'ses/';
                $ret_url = IntranetRsc::forceHttps(config('params.docBaseUrl')) . $base_dir . Str::replaceLast('/', '-', $value_nh) . $actualizado . '.' . $value_ext;
                break;

            case 'decretos': 
                $base_dir = 'dec/';
                $prefix = $value_a; // default
                switch ($value_a) {
                    case 'or': $prefix = 'o'; break;
                    case 'og': $prefix = 'g'; break;
                    case 'de_dec': $prefix = 'd'; break;
                    case 'de_ses': $prefix = 'd'; break;
                    case 'do': $prefix = 'do'; break;
                    case 're': $prefix = 'r'; break;
                    case 're_nor': $prefix = 'r'; break;
                    case 're_ses': $prefix = 'r'; break;
                    case 'le': $prefix = 'l'; break;
                }
                $ret_url = IntranetRsc::forceHttps(config('params.docBaseUrl')) . $base_dir . $prefix . Str::replaceLast('/', '-', $value_n) . $actualizado . '.' . $value_ext;
                break;
        }

        // Add no-cache var
        if (config('params.forceNoCacheDoc'))
            $ret_url = sprintf('%s?v=%s', $ret_url, md5(date_format(new \DateTime(), 'Ymdhisu')));

        return $ret_url;
    }

    /**
     * [baseTag description]
     * @param  [type] $base         [description]
     * @param  [type] $dec_promulga [description]
     * @param  [type] $recopila     [description]
     * @return [type]               [description]
     */
    public static function baseTag($base, $dec_promulga, $recopila) {
        if (strtolower($base) == 'normas') {
            return (strtolower($dec_promulga) != 'esp-pro' && strtolower($recopila) == 's')
                ? 'digesto'
                : 'normas';
        } else {
            return strtolower($base);
        }
    }

}