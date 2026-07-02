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

class FakeAttrib {

    /**
     * [tipoOrigenDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function tipoOrigenDesc($value)
    {
        if (array_key_exists($value, config('defaults.descripTipoOrigen'))) 
            return config('defaults.descripTipoOrigen')[$value];
        else
            return $value;
    }

    /**
     * [titularidadDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function titularidadDesc($value)
    {
        if (array_key_exists($value, config('defaults.descripTitularidad'))) 
            return config('defaults.descripTitularidad')[$value];
        else
            return $value;
    }

    /**
     * [estadoDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function estadoDesc($value)
    {
        if (array_key_exists($value, config('defaults.descripEstado'))) 
            return config('defaults.descripEstado')[$value];
        else
            return $value;
    }

    /**
     * [condicionUsoDesc description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    public static function condicionUsoDesc($value)
    {
        if (array_key_exists($value, config('defaults.descripCondicionUso'))) 
            return config('defaults.descripCondicionUso')[$value];
        else
            return $value;
    }

    /**
     * [descDinamica description]
     * @param  [type] $activo              [description]
     * @param  [type] $atrib_desc_dinamica [description]
     * @return [type]                      [description]
     */
    public static function descDinamica($activo, $atrib_desc_dinamica)
    {
        $attr_list = (get_class($activo) === "App\Activo")
            ? array_keys($activo->getAttributes())
            : array_keys(get_object_vars($activo));
        $desc_list = explode(',', $atrib_desc_dinamica);
        $desc = '';
        foreach ($desc_list as $attr)
            if (in_array($attr, $attr_list))
                $desc .= $activo->{trim($attr)} . ' ';
        return trim($desc);
    }

}