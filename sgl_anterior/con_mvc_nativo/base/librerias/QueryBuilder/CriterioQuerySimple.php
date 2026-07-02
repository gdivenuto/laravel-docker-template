<?php
class CriterioQuerySimple extends CriterioQuery {

	public $operadorLogico;
	public $valorParametro;

	/**
	 * Constructor de clase
	 * @param string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param string $pcolumna Nombre de la columna sobre la cual aplicar el criterio.
	 * @param string $poperadorLogico Operador lógico con el cual realizar la comparación. Pueden utilizarse las constantes IGUAL_A, DISTINTO_A, MAYOR_A, MAYOR_IGUAL_A, MENOR_A, MENOR_IGUAL_A.
	 * @param mixed $pvalorParametro Valor sobe el cual se compara el criterio.
	 */
	public function __construct($ptipo, $pcolumna, $poperadorLogico, $pvalorParametro) {
		parent::__construct($ptipo, $pcolumna);
		$this->operadorLogico = $poperadorLogico;
		$this->valorParametro = $pvalorParametro;
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarWhere($excluirNulos = true) {
		$resultado = sprintf("%s %s ?", BuilderHelper::agregarBackquote($this->columna), $this->operadorLogico);

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