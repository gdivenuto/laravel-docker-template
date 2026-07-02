<?php
/**
 * Capa de datos general.
 * Funciona como un híbrido de una clase singleton, conteniendo las instancias
 * a las distintas capas de datos específicas de los distintos subsistemas.
 */
class DB {
	
    private static $instancias = array(); //!< Contenedor de instancias singleton

    // --------------------------------------------------------------------------------------------
    // Metodos mágico para capturar las llamadas 
    // --------------------------------------------------------------------------------------------
    /**
     * Definición del método mágico __callStatic, utilizado al invocar dinamicamente las capas de datos.
     * @param  string $method Nombre del método a invocar.
     * @param  mixed $args   Paramámetros del método.
     * @return mixed         Resultado de la llamada.
     */
    public static function __callStatic($method, $args) {

        // si se invoca el método como 'DB::getInstanceDBXyz'
        if (substr($method, 0, strlen('getInstanceDB')) == 'getInstanceDB') 
            $instancia = str_replace('getInstanceDB', '', $method);
        else
            $instancia = $method; // si se invoca el método como 'DB::xyz()'

        // Nombre de la clase a instanciar.
        $instancia = 'DB'.ucfirst($instancia); 

        // Si no existe la instancia, la creo.
        if (!array_key_exists($instancia, self::$instancias)) 
            self::$instancias[$instancia] = new $instancia(); // Gracias al autoload, si la clase '$instancia' no existe, lanza una excepcion.
        
        // Devuelvo la instancia, segun patron 'singleton'.
        return self::$instancias[$instancia];
    }

    // --------------------------------------------------------------------------------------------
	// Metodos propios para patron Singleton
	// --------------------------------------------------------------------------------------------
	
	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct() {

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