<?php
/**
 * Generador optimizado de consultas a base de datos.
 * Permite la generación de query utilizando solamente los criterios de búsqueda especificados.
 * @author gvergra carlosgabrielvergara@gmail.com
 */
class SelectQueryBuilder extends BaseCRUDQueryBuilder {

	public $cabecera; 			//!< Cabecera del query. Contiene la instrucción SELECT, el conjunto de campos requeridos y de ser necesario, los JOIN y demás modificadores. Ejemplo: 'SELECT nombre, edad FROM persona'.
	public $pie; 				//!< Pie del query. Puede contener extras, sobre todo cuando se desea englobar el query principal dentro de un select por fuera del mismo.
	public $criteriosWhere;		//!< Array de elementos tipo CriteroQuery.
	public $criteriosGroupBy;	//!< Array de strings con criterio de agrupamiento. Cada elemento representa un agrupamiento.
	public $criteriosOrderBy;	//!< Array de strings con criterio de ordenamiento. Cada elemento representa un criterio.
	public $limiteCantidad;		//!< Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	public $limiteOffset;		//!< Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.

	/**
	 * Constructor de clase.
	 */
	public function __construct() {
		$this->criteriosWhere = new ListaCriteriosQuery();
		$this->criteriosGroupBy = array();
		$this->criteriosOrderBy = array();
		$this->limiteCantidad = null;
		$this->limiteOffset = null;

		$this->cabecera = "";
		$this->pie = "";
	}

	/**
	 * Agrega un criterio de ordenamiento a la consulta.
	 * @param  string $columna Nombre de la columna a ordenar.
	 * @param  string $orden   Sentido del ordenamiento. Pueden utlizarse las constantes ORDEN_ASCENDENTE y ORDEN_DESCENDENTE.
	 */
	public function agregarCriterioOrderBy($columna, $orden = ORDEN_ASCENDENTE) {
		$this->criteriosOrderBy[] = sprintf('%s %s ', BuilderHelper::agregarBackquote($columna), $orden);
	}

	/**
	 * Agrega un criterio de agrupamiento a la consulta.
	 * @param  string $columna Nombre de la columna o el criterio de agrupamiento.
	 */
	public function agregarCriterioGroupBy($columna) {
		$this->criteriosGroupBy[] = sprintf('%s ', BuilderHelper::agregarBackquote($columna));
	}

	/**
	 * Genera la porción del query correspondiente al criterio de ordenamiento.
	 * @return string
	 */
	private function generarCriterioOrderBy() {
		// Por cada columna, vamos armando un string que genera la clausula
		// ORDER BY del query
		$clausula = "";

		// Si no hay columnas, no hay clausula
		if ($this->criteriosOrderBy !== null)
		{
			if (count($this->criteriosOrderBy) > 0)
				$clausula = " ORDER BY ".implode(",", $this->criteriosOrderBy);
		}

		return $clausula;
	}

	/**
	 * Genera la porción del query correspondiente al criterio de agrupamiento.
	 * @return string
	 */
	private function generarCriterioGroupBy() {
		// Por cada columna, vamos armando un string que genera la clausula
		// GROUP BY del query
		$clausula = "";

		// Si no hay columnas, no hay clausula
		if ($this->criteriosGroupBy !== null)
		{
			if (count($this->criteriosGroupBy) > 0)
				$clausula = " GROUP BY ".implode(",", $this->criteriosGroupBy);
		}

		return $clausula;
	}

	/**
	 * Genera la porción del query correspondiente al modificador LIMIT.
	 * @return string
	 */
	private function generarLimit()
	{
		// Vamos armando un string que genera la clausula LIMIT del query
		$clausula = "";

		// si la cantidad es nula, no hago nada
		if ($this->limiteCantidad !== null)
		{
			$clausula = " LIMIT ".$this->limiteCantidad;

			// si el offset es nulo, no agrego "el corrimiento" (XXXX de mierda, me gusta el ingles).
			if ($this->limiteOffset !== null)
				$clausula = " LIMIT ".$this->limiteOffset.", ".$this->limiteCantidad;
		}

		return $clausula;
	}

	/**
	 * Genera el query optimizado en base a la configuración provista.
	 * @param  string $operadorWhere Operador de unión de los diferentes criterios de selección. Por defecto, es CRITERIO_AND.
	 * @return string
	 */
	public function getQuery($operadorWhere = CRITERIO_AND) {
		$query = '';

		$query .= $this->cabecera;
		if ($this->criteriosWhere->hayCriterios())
			$query .= " WHERE ".$this->criteriosWhere->generarWhere($operadorWhere);
		$query .= $this->pie; //TODO: el pie va aca? por ahora si...
		$query .= $this->generarCriterioGroupBy();
		$query .= $this->generarCriterioOrderBy();
		$query .= $this->generarLimit();
		// $query .= $this->pie; //TODO: el pie va aca? por ahora no...

		return $query;
	}

	/**
	 * Genera el indicador de tipo de dato de todos los criterios para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarBindParams($excluirNulos = true) {
		return $this->criteriosWhere->generarBindParams($excluirNulos);
	}

}

?>
