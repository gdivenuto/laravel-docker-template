<?php
/**
 * Generador optimizado de instrucciones de persistencia a base de datos.
 * Permite la generación de query determinando el mapeo de los campos.
 */
class DeleteQueryBuilder extends BaseCRUDQueryBuilder  {

	public $criteriosWhere;	//!< Array de elementos tipo CriteroQuery.

	/**
	 * Constructor de clase.
	 * @param string $pnombreTabla Nombre de la tabla sobre la cual se realizarán las rutinas de persistencia de datos.
	 */
	public function __construct($pnombreTabla = "") {
		parent::__construct($pnombreTabla);
		$this->criteriosWhere = new ListaCriteriosQuery();
	}

	/**
	 * Genera el query optimizado en base a la configuración provista.
	 * @param  string $operadorWhere Operador de unión de los diferentes criterios de selección. Por defecto, es CRITERIO_AND.
	 * @return string
	 */
	public function getQuery($operadorWhere = CRITERIO_AND) {
		$query = '';

		$query .= sprintf('DELETE FROM %s', BuilderHelper::agregarBackquote($this->nombreTabla));
		if ($this->criteriosWhere->hayCriterios())
			$query .= " WHERE ".$this->criteriosWhere->generarWhere($operadorWhere);

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