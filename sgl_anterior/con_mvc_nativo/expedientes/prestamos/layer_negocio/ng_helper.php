<?php
/* ****************************************************************************
 Helper de la capa de negocio
 
 Clase diseñada para ser estatica.
**************************************************************************** */

class ng_helper {
	
	function __construct() {
		
	}

	public static function arrayResultToInstance($resultArray, $className)
	{
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
					
			// Agrego la instancia al array de resultados
			$resultado[] = $instancia;
		}
		
		return $resultado;
	}
		
}

?>