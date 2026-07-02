<?php
/**
 * Clase que implementa el patrón singleton para centralización de la lógica de formateo de texto en PHP.
 *
 * @author XXXX
 */
class FormatText
{
    private static $instance;


    /**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
     * @return FormatText Instancia de la clase.
     */
    public static function GetInstance()
    {
        // Si la instancia no esta definida la creo, sino devuelvo la existente
        if (!isset(self::$instance))
        {
            $claseActual = __CLASS__;			// Obtengo la clase actual
            self::$instance = new $claseActual; // Creo una instancia
        }

		// Devuelvo la instancia existente.
        return self::$instance;
    }

     /**
     * Alias de GetInstance()
     * @return FormatText Instancia de la clase.
     */
    public static function get()
    {
        return self::GetInstance();
    }

    /**
     * Es invocado cuando se clona un instancia.
     * Con este método podemos emitir un mensaje de error y proceder a detener la ejecución del
     * script por operación inválida al intentar clonar una instancia de Singleton.
     *
     * E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
     */
    public function __clone()
    {
		trigger_error("Operación Inválida: No se puede clonar una instancia de ". get_class($this) .".", E_USER_ERROR );
    }

    /**
     * __sleep es invocado cuando un objeto es serializado se evita serializar una instancia de
     * Singleton
     */
    public function __sleep()
    {
        trigger_error("No se puede serializar una instancia de ". get_class($this) .".");
    }

    /**
     * __wakeup es invocado cuando un objeto es deserializado se evita deserializar una instancia
     * de Singleton
     */
    public function __wakeup()
    {
		trigger_error("No se puede deserializar una instancia de ". get_class($this) .".");
    }

	public function reemplazarPorHTML($cadena)
	{
		$cadena = str_replace("á","&aacute;",$cadena);
		$cadena = str_replace("é","&eacute;",$cadena);
		$cadena = str_replace("í","&iacute;",$cadena);
		$cadena = str_replace("ó","&oacute;",$cadena);
		$cadena = str_replace("ú","&uacute;",$cadena);
		$cadena = str_replace("ñ","&ntilde;",$cadena);
		$cadena = str_replace("Á","&Aacute;",$cadena);
		$cadena = str_replace("É","&Eacute;",$cadena);
		$cadena = str_replace("Í","&Iacute;",$cadena);
		$cadena = str_replace("Ó","&Oacute;",$cadena);
		$cadena = str_replace("Ú","&Uacute;",$cadena);
		$cadena = str_replace("Ñ","&Ntilde;",$cadena);
		$cadena = str_replace("ü","&uuml;",$cadena);
		$cadena = str_replace("Ü","&Uuml;",$cadena);
		$cadena = str_replace("@","&#64;",$cadena);
		$cadena = str_replace("°","&deg;",$cadena);
		$cadena = str_replace("º","&deg;",$cadena);
		$cadena = str_replace("ª","&deg;",$cadena);
		$cadena = str_replace('"',"&#34;",$cadena);

		return $cadena;
	}

    /**
     * Para convertir los saltos de linea y las tabulaciones en su respectiva etiqueta u operador html
     * @param string $textohtml
     * @return mixed
     */
    public static function limpiarEspaciosExternos($textohtml) {
        $textohtml = str_replace("\n", "<br>", $textohtml);
        $textohtml = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;", $textohtml);
        $textohtml = str_replace("&#10;", '', $textohtml);
        $textohtml = ltrim(rtrim($textohtml));

        return $textohtml;
    }


}
