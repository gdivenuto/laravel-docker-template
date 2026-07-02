<?php
/**
 * Clase abstracta con el comportamiento base de cualquer criterio para el SelectQueryBuilder.
 */
abstract class CriterioQuery {

	public $columna;
	public $tipo;

	/**
	 * Constructor de clase
	 * @param string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param string $pcolumna Nombre de la columna sobre la cual aplicar el criterio.
	 */
	public function __construct($ptipo, $pcolumna) {
		$this->tipo = $ptipo;
		$this->columna = $pcolumna;
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	abstract public function generarWhere($excluirNulos = true);

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	abstract public function obtenerTipoBindParam($excluirNulos = true);

}

?>