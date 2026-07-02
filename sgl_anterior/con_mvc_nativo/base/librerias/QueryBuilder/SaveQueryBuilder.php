<?php
/**
 * Generador optimizado de instrucciones de persistencia a base de datos.
 * Permite la generación de query determinando el mapeo de los campos.
 */
class SaveQueryBuilder extends BaseCRUDQueryBuilder {

	public $mapeoCampos; //!< Atributo de tipo ListaMapeoCampos, el cual representa el mapeo de los atributos de una clase contra los campos de una tabla.

	/**
	 * Constructor de clase.
	 * @param string $pnombreTabla Nombre de la tabla sobre la cual se realizarán las rutinas de persistencia de datos.
	 */
	public function __construct($pnombreTabla = "") {
		parent::__construct($pnombreTabla);
		$this->mapeoCampos = new ListaMapeoCampos();
	}

	/**
	 * Genera el query optimizado en base a la configuración provista.
	 * @return string
	 */
	public function getQuery($operadorWhere = CRITERIO_AND) {
		// el operador del where se ignora

		$query = sprintf('INSERT INTO %s %s ON DUPLICATE KEY UPDATE %s',
			BuilderHelper::agregarBackquote($this->nombreTabla),
			$this->mapeoCampos->generarMapeoInsert(),
			$this->mapeoCampos->generarMapeoUpdate());

		return $query;
	}

	/**
	 * Genera el indicador de tipo de dato de todos los criterios para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarBindParams($excluirNulos = true) {
		return $this->mapeoCampos->generarBindParams(); // no contemplo $excluirNulos
	}

}

?>