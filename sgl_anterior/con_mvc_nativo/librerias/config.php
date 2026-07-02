<?php
class Config
{
    // Atributo privado para determinadas variables
	private $vars;
    // Atributo privado estático para la instancia Singleton de Config
    private static $instance;
 
    private function __construct()
    {
        // Se inicializa como un array
    	$this->vars = array();
    }
 
    // Se asigna un valor a una variable determinada
    public function set($name, $value)
    {
        if (!isset($this->vars[$name]))
        {
            $this->vars[$name] = $value;
        }
    }
 
    // SE OBTIENE EL VALOR DE UN ATRIBUTO DETERMINADO
    public function get($name)
    {
        if (isset($this->vars[$name]))
        {
            return $this->vars[$name];
        }
    }
    
	/************************************************************************************
		Se implementa el Patron Singleton para mantener una única instancia y 
		poder acceder a sus valores desde cualquier sitio.
	/**********************************************************************************/
    public static function singleton()
    {
        // SI NO EXISTE LA INSTANCIA
        if (!isset(self::$instance))
        {
            // SE ASIGNA LA CLASE ACTUAL
            $c = __CLASS__;
            
            // SE CREA UNA INSTANCIA DE DICHA CLASE
            self::$instance = new $c;
        }
 
		// DEVUELVE LA INSTANCIA EXISTENTE
        return self::$instance;
    }
    
    /***************************************************************************************
		Es invocado cuando se clona un instancia.
		Con este método podemos emitir un mensaje de error y 
		proceder a detener la ejecución del script por operación inválida
		al intentar clonar una instancia de Singleton
		
		E_USER_ERROR: constante que contiene el mensaje de error generado por el usuario
    /*****************************************************************************************/
    public function __clone() 
    { 
		trigger_error("Operación Inválida: No se puede clonar una instancia de ". get_class($this) .".", E_USER_ERROR ); 
    } 
    
    /***************************************************************************************
		__sleep es invocado cuando un objeto es serializado
		se evita serializar una instancia de Singleton
    /*****************************************************************************************/
    public function __sleep()
    {
        trigger_error("No se puede serializar una instancia de ". get_class($this) ."."); 
    }
    
    /**************************************************************************************
		__wakeup es invocado cuando un objeto es deserializado
		se evita deserializar una instancia de Singleton
    /***************************************************************************************/
    public function __wakeup() 
    { 
		trigger_error("No se puede deserializar una instancia de ". get_class($this) ."."); 
    } 
}
?>
