<?php
/**
 * Clase que implementa el patrón singleton para las codificaciones y decodificaciones de JSON a instancias de clase y viceversa.
 *
 * @author kaleb
 * @author XXXX
 */
class JsonHelper
{
	private static $instance;

	// ************************************************************************
	// Definición de Constantes ***********************************************
	// ************************************************************************
	const JSON_CLASS_ATTRIBUTE_NAME = 'className'; //!< Nombre por defecto de atributo 'className'.

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************

    /**
     * Extrae las propiedades de la instancia independientemente de su visibilidad, devolviendo un array asociativo.
     * Si el elemento a extraer es un objeto, agrego ademas el atributo 'className'.
     * @param  mixed $instancia Instancia de la cual extraer sus propiedades.
     * @param  boolean $ignorarVisibilidad      Si es TRUE, extrae todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
     * @return array        Array asociativo con el valor de cada propiedad.
     */
    public function extraerPropiedades($instancia, $ignorarVisibilidad = true) {
        $propiedades = array();//[];

        // Si la instancia es un array...
        if (is_array($instancia)) {
        	foreach ($instancia as $clave => $elemento)
        		$propiedades[$clave] = self::get()->extraerPropiedades($elemento, $ignorarVisibilidad);
        }
        // Si la instancia es un objeto
        else if (is_object($instancia)) {
	        $origenReflection = new ReflectionClass(get_class($instancia));

	        foreach ($origenReflection->getProperties() as $property)
	        	if ($ignorarVisibilidad || $property->isPublic()) {
		           	$property->setAccessible(true);

		            $value = $property->getValue($instancia);
		            //$value = (is_string($value)) ? html_entity_decode($value, ENT_QUOTES) : $value;// fix para caracteres especiales
		            $name = $property->getName();

		            $propiedades[$name] = self::get()->extraerPropiedades($value, $ignorarVisibilidad);

		            /* este codigo queda redundante porque ya se verifica en un nivel anterior de recursividad
		            // Si la propiedad es un array, extraigo las propiedades de cada elemento.
		            if (is_array($value)) {
		                $propiedades[$name] = array();//[];

		                foreach ($value as $item) {
		                    if (is_object($item)) {
		                        $itemArray = self::get()->extraerPropiedades($item, $ignorarVisibilidad);
		                        $propiedades[$name][] = $itemArray;
		                    } else
		                        $propiedades[$name][] = $item;
		                }
		            }
		            // Si la propiedad es un objeto, extraigo sus propiedades.
		            else if(is_object($value))
		                $propiedades[$name] = self::get()->extraerPropiedades($value, $ignorarVisibilidad);

		            // Finalmente, serializo la propiedad.
		            else $propiedades[$name] = $value;
		            */
	        	}

	        // Si la instancia actual es un objeto, genero el atributo className.
        	$propiedades[self::JSON_CLASS_ATTRIBUTE_NAME] = get_class($instancia);
        }
        // Si no es un array o un objeto, es una variable simple.
        else
        	$propiedades = $instancia;

        return $propiedades;
    }

    /**
     * Codifica a JSON una instancia de clase. Agrega el atributo 'className' al resultado.
     * @param  mixed $instancia Instancia de clase a codificar
     * @param  boolean $ignorarVisibilidad      Si es TRUE, codifica todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
     * @param  integer $opciones           Opciones de codificación de JSON, iguales a las opciones de la funcion json_encode().
     * @return string JSON string con la instancia codificada.
     */
    public function serializar($instancia, $ignorarVisibilidad = true, $opciones = 0) {
   		return json_encode(self::get()->extraerPropiedades($instancia, $ignorarVisibilidad), $opciones);
    }

    /**
	 * Genera un hash de la clase instanciada para verificación de serializacion/deserializacion JSON.
	 * @return string Hash de la clase formado a partir del nombre de la clase y todos sus atributos (public, private y protected)
	 */
	public function generarHashClase($instancia, $ignorarVisibilidad = true) {
		// Extraigo las propiedades y agrego el "className"
		$atributos_keys = array_keys(self::get()->extraerPropiedades($instancia, $ignorarVisibilidad));

		asort($atributos_keys); // ordeno el array porque algunos navegadores cambian el orden de los atributos del JSON

		$firma_clase = sprintf("(%s)%s", get_class($instancia), implode(";", $atributos_keys));

		return md5($firma_clase);
	}

	/**
	 * Verifica un hash de la una clase instanciada contra un stdClass resultado de deserializacion JSON.
	 * @param  mixed   $instancia         Clase instanciada.
	 * @param  stdClass $jsonStdClass stdClass resultado de deserializacion JSON.
     * @param  boolean $ignorarVisibilidad      Si es TRUE, extrae todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
	 * @return bool True si la instancia actual y la stdClass son iguales, False en caso contrario.
	 */
	public function verificarHashClase($instancia, stdClass $jsonStdClass, $ignorarVisibilidad = true) {

		// La instancia debe tener el atributo "className"
		if (!property_exists($jsonStdClass, self::JSON_CLASS_ATTRIBUTE_NAME))
			return false;

		// El atributo "className" debe ser igual que la clase actual
		if (get_class($instancia) != $jsonStdClass->{self::JSON_CLASS_ATTRIBUTE_NAME})
			return false;

		// Si llegue hasta aqui, verifico el hash...
		// get_object_vars funciona solamente con atributos publicos, y como el stdClass solo tiene atributos publicos, no tengo problema.
		// Si utilizara el "extraerPropiedades" no funcionaria debido a que stdClass no tiene propiedades para Reflection (la clase no define propiedades)
		$atributos_keys = array_keys(get_object_vars($jsonStdClass));
		asort($atributos_keys); // ordeno el array porque algunos navegadores cambian el orden de los atributos del JSON
		$firma_clase = sprintf("(%s)%s", $jsonStdClass->{self::JSON_CLASS_ATTRIBUTE_NAME}, implode(";", $atributos_keys));

		return self::get()->generarHashClase($instancia, $ignorarVisibilidad) == md5($firma_clase);
	}

	/**
	 * Copia los atributos de una instancia origen en la instancia actual, sin importar la clase
	 * de la instancia origen. Solamente copia aquellos atributos que existan tanto en la instancia
	 * origen como en la instancia actual.
	 * @param  mixed $instanciaOrigen
	 * @param  mixed $instanciaDestino
     * @param  boolean $ignorarVisibilidad      Si es TRUE, extrae todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
	 * @return ClaseBase
	 */
	private function copiarPropiedades($instanciaOrigen, $instanciaDestino, $ignorarVisibilidad = true) {
		// Reflection para la instancia de Origen
		$origenReflection = new ReflectionObject($instanciaOrigen);
		$propiedadesOrigen = $origenReflection->getProperties();

		// Reflection para la clase destino (instanciaDestino)
		$destinoReflection = new ReflectionObject($instanciaDestino);

		foreach ( $propiedadesOrigen as $propOrigen ) {
			$propOrigen->setAccessible(true);
			$name = $propOrigen->getName();
			$value = $propOrigen->getValue($instanciaOrigen);

			if ($destinoReflection->hasProperty($name)) {
				$propDest = $destinoReflection->getProperty($name);
				$propDest->setAccessible(true);

				$propDestValue = $propDest->getValue($instanciaDestino);

				if (is_array($propDestValue)) {
					$tempArray = array();
					//foreach ($value as $elemento) hcd06 TIENE PHP VERSION 5.3
						//$tempArray[] = self::get()->stdClassToCustomClass($elemento);
					for ($i=0; $i < count($value); $i++)
						$tempArray[] = self::get()->stdClassToCustomClass($value[$i]);
					$propDest->setValue($instanciaDestino, $tempArray);
				}
				else if (is_object($propDestValue)) {
					$propDestValue = self::get()->copiarPropiedades($value, $propDestValue);
					$propDest->setValue($instanciaDestino, $propDestValue);
				} else
					$propDest->setValue($instanciaDestino, $value);

				// ***************************************************************
				// if (is_null($propDest->getValue($instanciaDestino)))
				// 	$cad = 'nulo';
				// else if (is_object($propDest->getValue($instanciaDestino)))
				// 	$cad = get_class($propDest->getValue($instanciaDestino));
				// else
				// 	$cad = gettype($propDest->getValue($instanciaDestino));
				// echo "[".$propDest->getName()." es $cad]<br>";
				// ***************************************************************
			}
		}

		return $instanciaDestino;
	}

	/**
	 * Convierte una instancia de stdClass a una clase específica, a partir de su 'className' y verificación de Hash.
	 * @param  stdClass  $pStdClass        Instancia de StdClass.
     * @param  boolean $ignorarVisibilidad      Si es TRUE, extrae todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
	 * @return mixed                      Instancia resultado.
	 */
	private function stdClassToCustomClass(stdClass $pStdClass, $ignorarVisibilidad = true) {
		if (!property_exists($pStdClass, self::JSON_CLASS_ATTRIBUTE_NAME))
			throw new Exception("Error en JsonHelper: la clase a decodificar no posee el atributo ".self::JSON_CLASS_ATTRIBUTE_NAME);

		$claseResultado = $pStdClass->{self::JSON_CLASS_ATTRIBUTE_NAME};

		$instancia = new $claseResultado();

		if (! self::get()->verificarHashClase($instancia, $pStdClass, $ignorarVisibilidad))
			throw new Exception("Error en JsonHelper: la clase a decodificar no es compatible con la definici&oacute;n de la clase ".$claseResultado);

		return self::get()->copiarPropiedades($pStdClass, $instancia);
	}

	/**
	 * Decodifica a una instancia de clase una cadena JSON. Quita el atributo 'className' del resultado. Verifica la integridad del deserializado a partir de la clase y su hash.
	 * @param  string $jsonString Cadena JSON a decodificar.
     * @param  boolean $ignorarVisibilidad      Si es TRUE, extrae todos los atributos. Si es FALSE, respeta la visibilidad de los atributos.
	 * @return mixed             Instancia resultado.
	 */
	public function deserializar($jsonString, $ignorarVisibilidad = true) {
		// Obtengo la instancia decodificando el json en un stdClass
		$jsonStdClass = json_decode($jsonString);

		return self::get()->stdClassToCustomClass($jsonStdClass);
	}

	// ************************************************************************
	// Metodos propios para patron Singleton
	// ************************************************************************

	/**
	 * Constructor privado, parte funcional del patron Singleton.
	 */
    private function __construct()
    {

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
