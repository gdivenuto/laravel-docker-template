<?php
/**
 * Clase base para todas las clases del modelo.
 * Contiene:
 * 	- Lógica de estado de instancia.
 * 	- Lógica de "Cast".
 * 	- Conversión a JSON y Base64.
 *  - Copia de atributos de instancias.
 *
 * @author XXXX
 *
 */

// Constantes de estado
define('IS_STABLE', 0);
define('IS_MODIFIED', 1);
define('IS_ADDED', 2);
define('IS_DELETED', 3);

abstract class ClaseBase {

	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $instanceState; //!< Lleva el control del estado de la instancia, para ser utilizado luego en el motor de persistencia.

	// ************************************************************************
	// Getters & Setters ******************************************************
	// ************************************************************************

	/**
	 * Método mágico getter, el cual encapsula el acceso a los atributos de las clases derivadas.
	 * @param  string $propertyName Nombre de la propiedad.
	 * @return mixed      Valor de la propiedad.
	 */
	public function __get ($propertyName) {
		$metodo = $this->generateMethodName("get", $propertyName);

		// Verifico la existencia de un método definido como getter.
		if (method_exists($this, $metodo)) return $this->{$metodo}();

		// Si no existe el método, busco la propiedad.
		else if (property_exists($this, $propertyName)) return $this->{$propertyName};

		// Si tampoco existe la propiedad, lanzo una excepción.
		else throw new Exception(sprintf("Error en clase %s: No existe el getter '%s()' o propiedad '%s'.", get_class($this), $metodo, $propertyName));
	}

	/**
	 * Método mágico setter, el cual encapsula el acceso a los atributos de las clases derivadas.
	 * @param string $propertyName  Nombre de la propiedad.
	 * @param mixed $propertyValue 	Valor de la propiedad.
	 */
	public function __set($propertyName, $propertyValue) {
		$metodo = $this->generateMethodName("set", $propertyName);

		// Verifico la existencia de un método definido como setter.
		if (method_exists($this, $metodo)) $this->{$metodo}($propertyValue);

		// Si no existe el método, busco la propiedad.
		else if (property_exists($this, $propertyName)) $this->{$propertyName} = $propertyValue;

		// Si tampoco existe la propiedad, lanzo una excepción.
		else throw new Exception(sprintf("Error en clase %s: No existe el setter '%s()' o propiedad '%s'.", get_class($this), $metodo, $propertyName));

		// Si la ejecución del setter llegó hasta aquí, entonces marco la instancia como modificada.
		$this->setInstanceState(IS_MODIFIED);
	}

	/**
	 * Cambia el estado de la instancia actual.
	 * @param integer $estado Estado de la instancia actual. Puede ser IS_STABLE, IS_MODIFIED, IS_ADDED o IS_DELETED.
	 */
	public function setInstanceState($estado) {
		if (($estado >= IS_STABLE) && ($estado <= IS_DELETED))
			$this->instanceState = $estado;
	}

	/**
	 * Obtiene el estado de la instancia actual.
	 * @return integer Estado de la instancia actual. Puede ser IS_STABLE, IS_MODIFIED, IS_ADDED o IS_DELETED.
	 */
	public function getInstanceState() {
		return $this->instanceState;
	}

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	public function __construct() {
		// Por defecto, las instancias se inicializan como estables.
		$this->instanceState = IS_STABLE;
	}

	/**
	 * Genera un nombre de método a partir de un nombre de propiedad.
	 * @param  string $prefijo      Prefijo del método.
	 * @param  string $propertyName Nombre de la propiedad
	 * @return String               Nombre de método siguiendo la nomenclatura de notación camello con primer caracter en minúsculas.
	 */
	protected function generateMethodName($prefix = "get", $propertyName) {
		return str_replace(" ", "_", $prefix.ucwords(str_replace("_", " ", $propertyName)));
	}

	/**
	 * A partir del estado actual de la instancia, se genera un checksum para comparación con otras instancias.
	 * @return string Código de verificación en base al estado actual de la instacia.
	 */
	public function generarChecksum() {
		return md5(serialize($this));
	}

	/**
	 * Verifica el checksum de la instancia actual contra un checksum dado.
	 * @param  string $checkSum Código de verificación en base al estado actual de la instacia.
	 * @return boolean               TRUE si son idénticos, FALSE en caso contrario.
	 */
	public function verificarChecksum($checksum) {
		return $this->generarChecksum() == $checksum;
	}

	/**
	 * Compara el estado de la instancia contra otra de tipo ClaseBase, utilizando el método generarChecksum().
	 * @param  ClaseBase $instancia Instancia contra la cual se desea comparar.
	 * @return boolean               TRUE si su estado es idéntico, FALSE en caso contrario.
	 */
	public function esIgual(ClaseBase $instancia) {
		return $this->verificarChecksum($instancia->generarChecksum());
	}

	/**
	 * Verifica si un valor es un 'integer' potencial.
	 * @param  mixed $val Valor a comprobar
	 * @return boolean    TRUE si es un posible 'integer', FALSE en caso contrario.
	 */
	public function esInteger($val) {
		return Validator::get()->esInteger($val);
	}

	/**
	 * Verifica si un valor es un 'float' o 'double' potencial. Los enteros se consideran float-compatible.
	 * @param   mixed $val Valor a comprobar
	 * @return boolean     TRUE si es un posible 'float' o 'double', FALSE en caso contrario.
	 */
	public function esFloat($val) {
		return Validator::get()->esFloat($val);
	}

	/**
	 * Verifica si un valor es un 'boolean' potencial. Los enteros que sean 1 o 0 se consideran boolean, siendo TRUE o FALSE respectivamente.
	 * @param   mixed $val Valor a comprobar
	 * @return boolean     TRUE si es un posible 'boolean', FALSE en caso contrario.
	 */
	public function esBoolean($val) {
		return Validator::get()->esBoolean($val);
	}

	/**
	 * [esVacio description]
	 * @param  [type] $val [description]
	 * @return [type]      [description]
	 */
	public function esVacio($val) {
		return Validator::get()->esVacio($val);
	}

	/**
	 * Devuelve un boolean a partir de un valor.
	 * @param  mixed $val Valor a partir del cual obtener el boolean.
	 * @return bool
	 */
	public function obtenerBoolean($val) {
		return Validator::get()->obtenerBoolean($val);
	}

	/**
	 * Toma una cadena con máscara 'Y-m-d' o 'Y-m-d H:i:s' y la convierte a un DateTime. Si falla, lanza una excepción.
	 * @param string $str_fecha Cadena con máscara 'Y-m-d' o 'Y-m-d H:i:s' a convertir a DateTime.
	 * @throws InvalidArgumentException
	 * @return DateTime Valor resultante de la conversión. Puede devolver null.
	 */
	protected function verificarDateTimeDesdeString($str_fecha)
	{
		return Validator::get()->verificarDateTimeDesdeString($str_fecha);
	}

	/**
     * Extrae las propiedades de la instancia independientemente de su visibilidad, devolviendo un array asociativo.
     * @param  mixed $instancia Instancia de la cual extraer sus propiedades.
     * @return array        Array asociativo con el valor de cada propiedad.
     */
    public function obtenerPropiedades($instancia) {
        $publicProperties = array();//[];

        $origenReflection = new ReflectionClass(get_class($instancia));

        foreach ($origenReflection->getProperties() as $property) {
            $property->setAccessible(true);

            $value = $property->getValue($instancia);
            $name = $property->getName();

            // Si la propiedad es un array, extraigo las propiedades de cada elemento.
            if (is_array($value)) {
                $publicProperties[$name] = array();//[];

                foreach ($value as $item) {
                    if (is_object($item)) {
                        $itemArray = $this->obtenerPropiedades($item);
                        $publicProperties[$name][] = $itemArray;
                    } else
                        $publicProperties[$name][] = $item;
                }

            // Si la propiedad es un objeto, extraigo sus propiedades.
            } else if(is_object($value))
                $publicProperties[$name] = $this->obtenerPropiedades($value);

            // Finalmente, serializo la propiedad.
            else $publicProperties[$name] = $value;
        }
        return $publicProperties;
    }
    /**
	 * Convierte la instancia actual a su correspondiente representacion en JSON.
	 * @return string
	 */
	public function ToJson()
	{
		return json_encode($this);
	}

	/**
	 * Obtiene una cadena en JSON e intenta regenerar el estado de la instancia a partir del elemento serializado.
	 * @param string $json_data
	 * @return mixed
	 */
	public function FromJson($json_data)
	{
		$stdData = json_decode($json_data);
		return $this->Cast($stdData);
	}

	/**
	 * Serializa la instancia empaquetando con Base64 la cadena resultado.
	 * @return string
	 */
	public function Serializar()
	{
		return base64_encode($this->ToJson());
	}

	/**
	 * Deserializa la instancia desempaquetando con Base64 el JSON de entrada.
	 * @param string $stringData Cadena en Base64 la cual contiene el JSON de la instancia a deserializar.
	 * @return mixed
	 */
	public function Deserializar($stringData)
	{
		return $this->FromJson(base64_decode($stringData));
	}

	/**
	 *
	 * @param unknown $claseDestino
	 * @param unknown $instanciaOrigen
	 * @return unknown
	 */
	protected function Cast($instanciaOrigen) {
		// Reflection para la instancia de Origen
		$origenReflection = new ReflectionObject($instanciaOrigen);
		$propiedadesOrigen = $origenReflection->getProperties();

		// Reflection para la clase destino (this)
		$destinoReflection = new ReflectionObject($this);

		foreach ( $propiedadesOrigen as $propOrigen ) {
			$propOrigen->setAccessible(true);
			$name = $propOrigen->getName();
			$value = $propOrigen->getValue($instanciaOrigen);

			if ($destinoReflection->hasProperty($name)) {
				$propDest = $destinoReflection->getProperty($name);
				$propDest->setAccessible(true);
				$propDest->setValue ($this, $value);
			}
			else
				$this->$name = $value;
		}
		return $this;
	}
}
?>
