<?php
class CriterioQueryLike extends CriterioQuery {

	public $prefijoLike;
	public $sufijoLike;
	public $valorParametro;

	/**
	 * Constructor de clase
	 * @param string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param string $pcolumna Nombre de la columna sobre la cual aplicar el criterio.
	 * @param string $pprefijoLike    Prefijo 'comodin' de la búsqueda con criterio LIKE
	 * @param mixed $pvalorParametro Valor a buscar
	 * @param string $psufijoLike     Sufijo 'comodin' de la búsqueda con criterio LIKE
	 */
	public function __construct($ptipo, $pcolumna, $pprefijoLike, $pvalorParametro, $psufijoLike) {
		parent::__construct($ptipo, $pcolumna);
		$this->prefijoLike = $pprefijoLike;
		$this->sufijoLike = $psufijoLike;
		$this->valorParametro = $pvalorParametro;
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarWhere($excluirNulos = true) {
		$resultado = sprintf("%s LIKE CONCAT('%s',?,'%s')", 
			BuilderHelper::agregarBackquote($this->columna), 
			$this->prefijoLike, 
			$this->sufijoLike);

		// Genero solo los criterios que no son null (depende de la forma de generar el where)
		if ($excluirNulos) 
			if ($this->valorParametro === null)
				$resultado = null;

		return $resultado;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function obtenerTipoBindParam($excluirNulos = true) {
		$resultado = $this->tipo;
		
		// Genero solo los tipos de parametro de aquellos criterios que no son null
		if ($excluirNulos) 
			if ($this->valorParametro === null)
				$resultado = '';

		return $resultado;
	}

}

?>