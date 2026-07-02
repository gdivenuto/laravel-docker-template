<?php
class CriterioQueryMultiple extends CriterioQuery {

	public $conjuntoParametros;

	/**
	 * Constructor de clase
	 * @param string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param string $pcolumna Nombre de la columna sobre la cual aplicar el criterio.
	 * @param array $pconjuntoParametros Arreglo con el conjunto de valores a comprobar.
	 */
	public function __construct($ptipo, $pcolumna, $pconjuntoParametros) {
		parent::__construct($ptipo, $pcolumna);
		$this->conjuntoParametros = $pconjuntoParametros;
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarWhere($excluirNulos = true) {
		// En el CriterioQueryMultiple se desestima el "excluirNulos"
		// Solamente se ignora cuando el conjunto de parametros es nulo o no tiene elementos.
		$resultado = null;

		if ($this->conjuntoParametros !== null) {
			if (count($this->conjuntoParametros) > 0) {
				$signosPregunta = implode(',', array_fill(0, count($this->conjuntoParametros), '?'));
				$resultado = sprintf("%s IN (%s)", BuilderHelper::agregarBackquote($this->columna), $signosPregunta);
			} 
		} 

		return $resultado;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function obtenerTipoBindParam($excluirNulos = true) {
		// En el CriterioQueryMultiple se desestima el "excluirNulos"
		// Solamente se ignora cuando el conjunto de parametros es nulo o no tiene elementos.
		$resultado = '';

		if ($this->conjuntoParametros !== null) {
			if (count($this->conjuntoParametros) > 0) {
				$resultado = '';
				foreach ($this->conjuntoParametros as $p) 
					$resultado .= $this->tipo;
			} 
		} 
		
		return $resultado;
	}

}

?>