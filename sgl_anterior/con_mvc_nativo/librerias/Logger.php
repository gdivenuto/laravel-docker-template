<?php
// Configuracion de rutas
require_once($_SERVER['DOCUMENT_ROOT'].'/sgl/config/path_config.php');

/**
 * Clase que implementa el patrón singleton para centralización de la lógica de logueo de errores e
 * información desde los scripts en PHP.
 */
class Logger
{
    private static $instance;
    private $extensionPorDefecto;
    private $tamanioIdIncremental;
    private $identificadorPorDefecto;

	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {
		$this->identificadorPorDefecto = 'sin_nombre'; // Identificador por defecto.
		$this->extensionPorDefecto = 'log'; // Extension por defecto para los logs.
        $this->tamanioIdIncremental = 4;    // Cantidad de digitos para el id autoincremental.
    }

    /**
     * Obtiene el nombre del archivo siguiente segun el contenido del directorio de log.
     * @param string $identificador Identificador del log.
     * @return string Nombre del archivo de log incremental.
     */
    private function ObtenerArchivoSiguiente($identificador)
    {
    	// Busco los archivos con el mismo identificador
    	$iterador = new GlobIterator(PATH_SGL_LOG.$identificador.'.*.'.$this->extensionPorDefecto, FilesystemIterator::KEY_AS_FILENAME);

    	// PARCHE OFICIAL!!! Si no hay ocurrencias, el $iterator->count() lanza una LogicException
    	try {
    		$cantidad = $iterador->count();
    	} catch (LogicException $le) {
    		$cantidad = 0;
    	}

    	// Resultado
    	$nombreArchivo = '';

    	// Si hay ocurrencias, busco el último valor.
    	if ($cantidad > 0)
    	{
    		$ultimoIdentificador = 0;
    		foreach ($iterador as $item)
    		{
    			$ocurrencias = Array();
    			if (preg_match_all('|\.([0-9]+)\.'.$this->extensionPorDefecto.'$|i', $iterador->key(), $ocurrencias, PREG_PATTERN_ORDER) > 0)
    			{
    				// La ocurrencia general esta en $ocurrencias[0][0], mientras que el numero esta en $ocurrencias[1][0]
    				if (intval($ocurrencias[1][0]) > $ultimoIdentificador)
    					$ultimoIdentificador =  intval($ocurrencias[1][0]);
    			}
    		}

    		// Genero el nombre de archivo con el último valor encontrado +1.
    		$nombreArchivo = $identificador.'.'
    				.substr(str_pad($ultimoIdentificador+1, $this->tamanioIdIncremental, "0", STR_PAD_LEFT), (-1) * $this->tamanioIdIncremental).'.'
    				.$this->extensionPorDefecto;
    	}
    	else
    		// No esta? genero el nombre con el identificador 1.
    		$nombreArchivo = $identificador.'.'
    				.substr(str_pad(1, $this->tamanioIdIncremental, "0", STR_PAD_LEFT), (-1) * $this->tamanioIdIncremental).'.'
    				.$this->extensionPorDefecto;

    	return $nombreArchivo;
    }

    /**
     * Genera un archivo de log con la información provista.
     * @param string $identificador Identificador del log. Termina siendo el que dicta el nombre del archivo de log.
     * @param mixed $data Datos a guardar en el log.
     * @param bool $incremental Si es true, genera archivos de log autoincrementales. Sino, genera un único archivo (el cual se pisa en multiples escrituras para el mismo identificador).
     */
    public function Log($identificador, $data, $incremental = true, $path_sgl_log = PATH_SGL_LOG)
    {
    	// Antes que nada normalizo el identificador, que no puede contener puntos.
    	// Los puntos se reemplazan por guión bajo.
    	if (trim($identificador) == "")
    		$id = $this->identificadorPorDefecto;
    	else
    		$id = str_replace('.', '_', trim($identificador));

		// Genero el nombre del archivo
		if ($incremental)
			$archivoLog = $path_sgl_log.$this->ObtenerArchivoSiguiente($id);
		else
			$archivoLog = $path_sgl_log.$id.'.'.$this->extensionPorDefecto;

		// Guardo los datos al archivo de log
    	fputs(fopen($archivoLog, 'w'), print_r($data, true));
    }

    /**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
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
