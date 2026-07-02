<?php
namespace App\Helpers;

/**
 * IntranetRsc - Helper
 *
 * Esta clase se utiliza para modificar los URI correspondientes a recursos 
 * externos a la aplicación cuando esta es accedida desde la intranet, 
 * generalmente haciendo cambios de 'http scheme' del tipo http->https o 
 * viceversa.
 * 
 */

class IntranetRsc {

    /**
     * Si la dirección remota es igual a la provista como argumento,
     * convierte un URL forzando su 'http scheme'.
     * @param  [type] $remote_addr_filter [description]
     * @param  [type] $url         [description]
     * @param  string $scheme      [description]
     * @return [type]              [description]
     */
    public static function forceUriScheme($remote_addr_filter, $url, $scheme = 'http')
    {
        // FIX: si se utiliza \Request::ip(), el framework trabaja con el header X-Forwarded-For
        // el cual devuelve la IP real del cliente, cuando lo que yo necesito es la ip del host
        // 'directo' que realizo la peticion, para detectar el proxy reverso.
        // Para conseguir esto, se utiliza $_SERVER['REMOTE_ADDR']
        /*
        return (\Request::ip() == $remote_addr_filter)
            ? preg_replace('/^http[s]{0,1}:\/\/(.*)$/i', $scheme.'://$1', $url)
            : $url;
        */
        if (isset($_SERVER) && array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return ($_SERVER['REMOTE_ADDR'] == $remote_addr_filter)
                ? preg_replace('/^http[s]{0,1}:\/\/(.*)$/i', $scheme.'://$1', $url)
                : $url;
        } else {
            return $url;
        }
    }

    /**
     * Si la dirección remota es la definida en params.revProxySrcIp,
     * convierte un URL forzando el 'http scheme' a 'http'.
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function forceHttp($url) 
    {
        return self::forceUriScheme(config('params.revProxySrcIp'), $url, 'http');
    }

    /**
     * Si la dirección remota es la definida en params.revProxySrcIp,
     * convierte un URL forzando el 'http scheme' a 'https'.
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function forceHttps($url) 
    {
        return self::forceUriScheme(config('params.revProxySrcIp'), $url, 'https');
    }

}