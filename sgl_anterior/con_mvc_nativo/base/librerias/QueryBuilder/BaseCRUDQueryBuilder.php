<?php

abstract class BaseCRUDQueryBuilder {
	
	public $nombreTabla; //!< Nombre de la tabla sobre la cual se realizarán las rutinas de persistencia de datos.

	/**
	 * Constructor de clase.
	 * @param string $pnombreTabla Nombre de la tabla sobre la cual se realizarán las rutinas de persistencia de datos.
	 */
	public function __construct($pnombreTabla = "") {
		$this->nombreTabla = $pnombreTabla;
	}

	/**
	 * Genera el query optimizado en base a la configuración provista.
	 * @param  string $operadorWhere Operador de unión de los diferentes criterios de selección. Por defecto, es CRITERIO_AND.
	 * @return string
	 */
	abstract public function getQuery($operadorWhere = CRITERIO_AND);

	/**
	 * Genera el indicador de tipo de dato de todos los criterios para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	abstract public function generarBindParams($excluirNulos = true);

	/**
	 * Genera una cadena con un reporte del query que se puede generar a partir de la configuración del builder.
	 * @return string Reporte del query generado.
	 */
	public function logQuery($operadorWhere = CRITERIO_AND, $excluirNulos = true) {
		$query  = "--------------------------------------------------------------------------------\n";
		$query .= "Query\n--------------------------------------------------------------------------------\n";
		$query .= $this->getQuery($operadorWhere);
		$query .= "\n--------------------------------------------------------------------------------\n\n";
		$query .= "--------------------------------------------------------------------------------\n";
		$query .= "Params\n--------------------------------------------------------------------------------\n";

		$parametros = $this->generarBindParams($excluirNulos);
		foreach ($parametros as $key => $value) {
			$query .= sprintf("[%s] = %s\n", $key, (is_null($value)) ? '<NULL>' : $value);
		}

		$query .= "\n--------------------------------------------------------------------------------";

		return $query;
	}
}

?>