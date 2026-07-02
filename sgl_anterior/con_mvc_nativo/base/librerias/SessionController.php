<?php
/**
 * Clase que implementa el patrón singleton para centralización de la lógica de manejo de sesiones
 * desde los scripts en PHP.
 * Posee ademas un mecanísmo que aísla las variables de sesión por aplicación, para que dos instancias
 * de Kraken puedan coexistir en el mismo servidor.
 * 
 * @author kaleb
 */
class SessionController
{
    private static $instance;
 
	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {
    }

    /**
     * Determina si la sesion esta iniciada. Funciona en múltiples versiones de PHP.
     * @return  bool Devuelve true si la sesión se encuentra iniciada, false en caso contrario.
     */
    public function sesionIniciada()
    {
		if (php_sapi_name() !== 'cli') {
			if (version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}

    /**
     * Inicia la sesion si no estuviera iniciada.
     */
    public function iniciarSesion()
    {
    	if (!$this->sesionIniciada())
    		session_start();
    }

    /**
     * Guarda en sesión una variable de tipo simple (string, int, float, etcétera) en sesión.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a guardar.
     * @param  mixed $valor          Valor a guardar.
     */
    public function guardar($nombreVariable, $valor)
    {
    	if ($this->sesionIniciada())
    		$_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)] = $valor;
    	else
    		throw new Exception("Error en SessionController.guardar: sesi&oacute;n no iniciada.");
    }

    /**
     * Guarda en sesión la variable predefinida 'MENSAJE_ERROR' de tipo string y 'NUMERO_ERROR' de tipo int.
     * Preformatea un mensaje de error incluyendo el número del mismo.
     * @param  string $mensajeError [description]
     * @param  int $numeroError  [description]
     */
    public function guardarError($mensajeError, $numeroError = null) {
        $nstr = (is_null($numeroError)) ? '' : sprintf('(%s) ', $numeroError);
        $this->guardar('MENSAJE_ERROR', sprintf('%s%s', $nstr, $mensajeError));
        
        $n = (is_null($numeroError)) ? -1 : $numeroError;
        $this->guardar('NUMERO_ERROR', $n);
    }

    /**
     * Elimina de sesión la variable predefinida 'MENSAJE_ERROR' de tipo string y 'NUMERO_ERROR' de tipo int.
     */
    public function eliminarError() {
        $this->eliminar('MENSAJE_ERROR');
        $this->eliminar('NUMERO_ERROR');
    }

    /**
     * Guarda en sesión una instancia de un objeto de tipo ClaseBase, serializándolo, comprimiéndolo y codificándolo en base64.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a guardar.
     * @param  ClaseBase $instancia      Instancia a guardar.
     */
    public function guardarSerializado($nombreVariable, ClaseBase $instancia)
    {
    	// Serializo, comprimo y guardo como base64.
        $this->guardar($nombreVariable, base64_encode(gzdeflate(serialize($instancia), 9)));
    }

   /**
     * Obtiene de la sesión una variable de tipo simple (string, int, float, etcétera).
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a obtener.
     * @return mixed                 Valor de la variable obtenida.
     */
    public function obtener($nombreVariable)
    {
        if ($this->sesionIniciada())
        {
            if (isset($_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)]))
                return $_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)];
            else
                throw new Exception("Error en SessionController.obtener: variable de sesion $nombreVariable no encontrada.");
        }
        else
            throw new Exception("Error en SessionController.obtener: sesi&oacute;n no iniciada.");
    }

    /**
     * Obtiene de la sesión una instancia de un objeto de tipo ClaseBase.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a obtener.
     * @param  ClaseBase $instancia      Instancia sobre la cual volcar el valor de la variable de sesion.
     * @return mixed                 Instancia sobre la cual se volcó el valor de la variable de sesion.
     */
    public function obtenerSerializado($nombreVariable, ClaseBase $instancia)
    {
    	return unserialize(gzinflate(base64_decode($this->obtener($nombreVariable))));
    }
 
    /**
     * Elimina una variable de sesión.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a eliminar.
     */
    public function eliminar($nombreVariable)
    {
    	if ($this->sesionIniciada())
    	{
    		//if (isset($_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)]))
    			unset($_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)]);
    		//else
	    	//	throw new Exception("Error en SessionController.eliminar: variable de sesion $nombreVariable no encontrada.");
    	}
    	else
    		throw new Exception("Error en SessionController.eliminar: sesi&oacute;n no iniciada.");
    }

    /**
     * Determina si existe una determinada variable de sesión.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  string $nombreVariable Nombre de la variable de sesión a verificar.
     * @return bool  Devuelve true si la variable existe, false en caso contrario.
     */
    public function existe($nombreVariable)
    {
    	return isset($_SESSION[strtoupper(KRAKEN_SESSION_PREFIX.$nombreVariable)]);
    }

    /**
     * Determina si existe un conjunto de variables de sesión determinados.
     * La referencia al nombre de la variable, es CASE INSENSITIVE (ignora mayúsculas y minúsculas).
     * @param  array  $variables Array de strings con los indetificadores de variables de sesión a comprobar.
     * @return bool  Devuelve true si todas las variables existen, false en caso contrario.
     */
    public function existen(array $variables)
    {
    	$flagExisten = true;
    	foreach ($variables as $v) {
    		$flagExisten = $flagExisten && $this->existe($v);
    	}
    	return $flagExisten;
    }

    /**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
     * @return SessionController Instancia de la clase SessionController.
     */
    public static function get()
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
}
?>
