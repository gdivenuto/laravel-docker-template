<?php
// ************************************************************************
// Definicion de constantes para validar parámetros ***********************
// ************************************************************************
// basico, permite todas las letras, numeros y signos de puntuación
// (Excluye acentos, ñ y ü)
define('PATRON_BASICO', "/^[\w\s[:punct:]]+$/");

// solo numeros
define('PATRON_NUMEROS', "/^[0-9]+$/");

// solo letras (mayusculas y minúsculas), SIN caracteres acentuados
define('PATRON_LETRAS', "/^[a-z]*$/i");

// alfanumérico, al menos un caracter
define('PATRON_ALFANUM', "/^[a-zA-Z0-9]+$/");

// alfanumérico extendido, al menos un caracter
$signos_validos=preg_quote("áéíóúüñçÁÉÍÓÚÜÑÇ", "/");
define('PATRON_ALFANUM_EXT', "/^[a-zA-Z0-9\s_\.-".$signos_validos."]+$/");

// usuario (todas las letras, numeros y algunos signos), al menos 3 caracteres
$signos_validos=preg_quote("@.!#$%&'*+-/=?^_`{|}~", "/");
define('PATRON_USUARIO', "/^[a-zA-Z0-9".$signos_validos."]{3,}$/");

// password (todas las letras, numeros y algunos signos), al menos 8 caracteres <-- limite actualmente desactivado
$signos_validos=preg_quote("áéíóúüñçÁÉÍÓÚÜÑÇ@.,;¡!#$%&'*+-/=¿?^_`{|}~[]()<>\\", "/");

//define('PATRON_PASSWORD', "/^[a-zA-Z0-9".$signos_validos."]{8,}$/");
define('PATRON_PASSWORD', "/^[a-zA-Z0-9".$signos_validos."]*$/");

// letras (mayusculas y minúsculas), numeros, blancos, signos de puntuación y "/" para fechas, CON caracteres acentuados (áéíóúñÁÉÍÓÚÑ)
define('PATRON_SEGURO_SIGNOS', "/^[0-9a-záéíóúüñçÁÉÍÓÚÜÑÇ\xE1\xE9\xED\xF3\xFA\xC1\xC9\xCD\xD3\xDA\xF1\xD1\(\)\.,;\/\-\s]*$/i"); 

// tipo de expediente
define('PATRON_TIPO_EXPEDIENTE', "/^(N|E|R|D|O)$/");

// tipo de expediente antecedente
define('PATRON_TIPO_EXPEDIENTE_ANTECEDENTE', "/^(E|N|D|G)$/");

// solo letras (mayusculas y minúsculas), SIN caracteres acentuados, AL MENOS 1
define('PATRON_LETRAS_NO_VACIO', "/^[a-z]+$/i");
	
// solo letras (mayusculas y minúsculas) y dígitos
define('PATRON_CODIGO_ALFANUMERICO_NO_VACIO', "/^[a-z0-9]+$/i");

// formato fecha y hora en string (compatible MySQL) 
define('PATRON_FECHA_HORA', "/^[0-9]{4,4}-[0-9]{2,2}-[0-9]{2,2} [0-9]{2,2}:[0-9]{2,2}:[0-9]{2,2}$/i");

// letras posibles para estado de prestamo de expediente
define('PATRON_ESTADO_PRESTAMO_EXPEDIENTE', "/^(S|P|D|A)$/");
	
// letras posibles para estado de prestamo de solicitud de expediente externo 
define('PATRON_ESTADO_SOLICITUD_EXPEDIENTE_EXTERNO', "/^(SHCD|SEE|IEE|DEE|AEE)$/");

/**
 * Clase que implementa el patrón singleton para centralización de la lógica de validación de parámetros
 * desde los scripts en PHP.
 * 
 * @author kaleb
 */
class Validator
{
	private static $instance;

	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	// Si esta habilitado, los errores de validaciones de parametros se guardan en el 
	// log de apache. 
	const LOG_PHP_VALIDATION_ERROR = true; // false;
	
	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {
    }

	/**
	 * Valida un parámetro; si no se cumple la expresion regular, lanza una excepción.
	 * @param string $valor Valor a validar.
	 * @param string $patron Expresión regular a utilizar para validación.
	 * @param boolean $permitirNull Indica si se permiten valores nulos ademas del patrón a utilizar.
	 * @param string $nombreParametro Opcional: nombre del parámetro que se esta validando.
	 * @return bool
	 * @throws UnexpectedValueException
	 */
	public function validar($valor, $patron, $permitirNull = false, $nombreParametro = '')
	{
		$aux = $valor;
		
		// Si se desea validar mediante un patron.
		if ( trim($patron) != '' )
		{
			if (($permitirNull) && ($valor == null))
				return null;
			else
				// Se evalúa con el patrón
				if (preg_match($patron, $aux))
					return $aux;
				else
				{
					$textoError = ($nombreParametro != '') ? "Parametro: $nombreParametro, Patron: $patron, Valor: $aux" : "Patron: $patron, Valor: $aux";

					// Si esta activada la validación, guardo en el log.
					if (self::LOG_PHP_VALIDATION_ERROR)
						error_log("[Validator] Fallo en validacion de parametro. $textoError");					
					
					throw new Exception("Error en validación: Parametro: $nombreParametro");
				}
		}	
		else
			return $aux;
	}

	/**
	 * Toma un parametro y ejecuta un filtro de saneo del mismo.
	 * @param  mixed $parametro Parametro a sanear.
	 * @return mixed            Parametro saneado.
	 */
	public function sanear($parametro) {
		// si el parametro es nulo, lo omito
		if (!is_null($parametro))
			$parametro = filter_var($parametro, FILTER_SANITIZE_SPECIAL_CHARS);

		return $parametro;
	}

	/**
	 * Toma un arreglo con un conjunto de parametros ejecuta un filtro de saneo de los mismos. 
	 * @param  array $paramArray Array de parametros a sanear.
	 * @param  array $arrayExclusion Array de strings con los identificadores de los parametros a omitir al momento de ejecutar el saneo.
	 * @return array                 Arreglo de parametros saneado.
	 */
	public function sanearConjunto($paramArray, $arrayExclusion = null) {
		// si el array es nulo, lo creo 
		if (is_null($arrayExclusion))
			$arrayExclusion = array();

		foreach ($paramArray as $clave => $valor) {
			if (!in_array($clave, $arrayExclusion))
				$paramArray[$clave] = self::getInstance()->sanear($paramArray[$clave]);
		}

		return $paramArray;
	}

	/**
	 * Verifica si un valor es un 'integer' potencial.
	 * @param  mixed $val Valor a comprobar
	 * @return boolean    TRUE si es un posible 'integer', FALSE en caso contrario.
	 */
	public function esInteger($val) {
		if (!is_scalar($val) || is_bool($val)) return false;
		
		if (is_float($val + 0) && ($val + 0) > PHP_INT_MAX) return false;

		return is_float($val) ? false : preg_match('~^((:?+|-)?[0-9]+)$~', $val);
	}

	/**
	 * Verifica si un valor es un 'float' o 'double' potencial. Los enteros se consideran float-compatible.
	 * @param   mixed $val Valor a comprobar
	 * @return boolean     TRUE si es un posible 'float' o 'double', FALSE en caso contrario.
	 */
	public function esFloat($val) {
		return is_numeric($val);
	}

	/**
	 * Verifica si un valor es un 'boolean' potencial. Los enteros que sean 1 o 0 se consideran boolean, siendo TRUE o FALSE respectivamente.
	 * @param   mixed $val Valor a comprobar
	 * @return boolean     TRUE si es un posible 'boolean', FALSE en caso contrario.
	 */
	public function esBoolean($val) {
		if (is_int($val))
			return ($val == 0 || $val == 1);
		else
			return is_bool($val);
	}

	/**
	 * Devuelve un boolean a partir de un valor.
	 * @param  mixed $val Valor a partir del cual obtener el boolean.
	 * @return bool
	 */
	public function obtenerBoolean($val) {
		if (!$this->esBoolean($val))
			throw new InvalidArgumentException("Imposible convertir el valor a boolean: ".$val);

		if (is_int($val))
			return ($val == 1);
		else 
			return $val;
	}

	/**
	 * [esVacio description]
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public function esVacio($val) {
		if (is_null($val)) 
			return true;
		else {
			if (is_string($val))
				return $val === "";
			else
				return false; // los valores numericos y boolean nunca son vacios (si es que no son nulos...)
		}
	}

	/**
	 * Toma una cadena con máscara 'Y-m-d' o 'Y-m-d H:i:s' y la convierte a un DateTime. Si falla, lanza una excepción.
	 * @param string $str_fecha Cadena con máscara 'Y-m-d' o 'Y-m-d H:i:s' a convertir a DateTime.
	 * @throws InvalidArgumentException
	 * @return DateTime Valor resultante de la conversión. Puede devolver null.
	 */
	public function verificarDateTimeDesdeString($str_fecha)
	{
		if (is_null($str_fecha) || empty($str_fecha))
			return null;
		else
		{
			// Pruebo el formato de fecha 'Y-m-d'
			$error_fecha = date_parse_from_format('Y-m-d', $str_fecha);
			if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0)) {

				// Si falla, pruebo el formato de fecha 'Y-m-d H:i:s'
				$error_fecha = date_parse_from_format('Y-m-d H:i:s', $str_fecha);
				if (($error_fecha['error_count'] > 0) || ($error_fecha['warning_count'] > 0))
					throw new InvalidArgumentException("Conversión de fecha inválida. Fecha: ".$str_fecha);
				else
					return DateTime::createFromFormat('Y-m-d H:i:s', $str_fecha);

			} else
				return DateTime::createFromFormat('Y-m-d', $str_fecha);
		}
	}

	/**
	 * Convierte una fecha al formato necesario para "mostrar" en la Vista, por defecto 'dd/mm/yyyy'
	 * 
	 * @param  string  $pfecha          Cadena con la fecha a convertir
	 * @param  string  $formato_destino Formato deseado, por defecto 'dd/mm/yyyy'
	 * @param  boolean $exceptionOnNull Determina si se desea lanzar una excepción en caso de nulidad, por defecto NO
	 * @return string                   Cadena con la fecha convertida
	 */
	public function convertirAFechaVista($str_fecha, $formato_destino = 'd/m/Y', $exceptionOnNull = false)
	{
		// Se verifica si la fecha es de formato 'yyyy-mm-dd' y devuelve un DateTime
		$fecha_aux = $this->verificarDateTimeDesdeString($str_fecha);

		// Si el objeto DateTime NO es nulo
		if ( !is_null($fecha_aux) )
			return $fecha_aux->format($formato_destino);
		else
			// Si se permite lanzar una excepción ante una nulidad 
		    if ($exceptionOnNull)
				throw new InvalidArgumentException("Conversión de fecha inválida. Fecha: ".$str_fecha);
		    else
				return ''; // Devuelve cadena vacía para "mostrar" en un campo o grilla, NO en un form de edición
	}

	/**
	 * Se verifica el valor determinado, de no poseerlo devuelve un valor por defecto
	 * 
	 * @param  mixed $valor             Valor a evaluar. Variable por referencia.
	 * @param  mixed $default Valor por defecto
	 * @return mixed                    Valor verificado
	 */
	public function obtenerDefault(&$valor, $default = null)
	{
		// Si está seteado el valor...
		if (isset($valor))
			// Si el valor es nulo, devuelve el valor por defecto, sino su contenido respectivo 
		    return (is_null($valor)) ? $default : $valor;
		else
			// Si no esta seteado, devuelvo el valor por defecto
		    return $default;
	}

	/**
     * Se implementa el patrón Singleton para mantener una única instancia y poder acceder a sus
     * valores desde cualquier script.
     * @return Validator Instancia de la clase.
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
     * Alias de get()
     * @return Validator Instancia de la clase.
     */
    public static function getInstance() {
        return self::get();
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