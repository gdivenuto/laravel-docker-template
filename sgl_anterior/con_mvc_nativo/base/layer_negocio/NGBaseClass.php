<?php
/**
 * Clase base (abstracta) para toda las capas de negocio.
 */
abstract class NGBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	function __construct() {

	}

	/**
	 * Checks if a folder exist and return canonicalized absolute pathname (sort version)
	 * @param string $folder the path being checked.
	 * @return mixed returns the canonicalized absolute pathname on success otherwise FALSE is returned
	 */
	protected function folderExist($folder)
	{
		// Get canonicalized absolute pathname
		$path = realpath($folder);

		// If it exist, check if it's a directory
		return ($path !== false AND is_dir($path)) ? $path : false;
	}

	/**
	 * Convierte un array de resultados a una coleccion de instancias determinadas.
	 * @param  array $resultArray Array de resultados (elementos con clave->valor) a convertir.
	 * @param  string $className   Nombre de la clase que completara la colección resultante.
	 * @return array              Colección de intancias de tipo "$className".
	 */
	protected function arrayResultToInstance(&$resultArray, $className)
	{
		// Si por algun extraño motivo la referencia a $resultArray es nula,
		// devuelvo un null.
		// VER más adelante si es necesario lanzar una excepción !!!
		if (is_null($resultArray))
			return null;

		// La magia de este método se basa en convertir un array asociativo
		// en un array de instancias, utilizando reflection para setear todos los
		// atributos automaticamente.
		$resultado = array();

		// Obtengo el constructor de la clase
		$class = new ReflectionClass($className);

		// recorro todas las filas
		foreach ($resultArray as $fila)
		{
			// Creo una instancia de la clase
			$instancia = $class->newInstance();

			foreach ($fila as $clave => $valor)
				// Si existe la propiedad en la instancia, la seteo
				if (property_exists($className, $clave))
					$instancia->{$clave} = $valor;

			// Si la clase es de tipo ClaseBase, ademas, la seteo como estable
			if ($instancia instanceof ClaseBase)
				$instancia->setInstanceState(IS_STABLE);

			// Agrego la instancia al array de resultados
			$resultado[] = $instancia;
		}

		return $resultado;
	}

	/**
	 * Convierte un array de resultados a una coleccion de instancias de tipo stdClass.
	 * @param  array $resultArray Array de resultados (elementos con clave->valor) a convertir.
	 * @return array              Colección de intancias de tipo "stdClass".
	 */
	protected function arrayResultToStdClass(&$resultArray)
	{
		$resultado = [];
        foreach ($resultArray as $row) {
			$object = new stdClass();
            foreach ($row as $key => $value) {
            	$object->$key = $value;
            }
            $resultado[] = $object;
        }
        return $resultado;
	}

	/**
	 * Se convierte una fecha al formato dia/mes/anio completo
	 * @param  string $fecha 	Fecha a formatear
	 * @return string        	Fecha formateada
	 */
	protected function formatearFecha($fecha)
	{
	    if ($fecha) {
			if ($fecha != '0000-00-00') {
				$fec_partes = explode("-",$fecha);
				return $fec_partes[2].'/'.$fec_partes[1].'/'.$fec_partes[0];
			} else
				return '';
	    } else
			return '';
	}

}
?>
