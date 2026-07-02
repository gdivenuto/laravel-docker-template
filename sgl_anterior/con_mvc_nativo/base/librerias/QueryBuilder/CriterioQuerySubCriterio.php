<?php
class CriterioQuerySubCriterio extends CriterioQuery {

	public $operadorWhere;
	public $listaCriterios;

	/**
	 * Constructor de clase
	 * @param string $poperadorWhere Operador lógico con el cual unir los criterios del WHERE. Puede ser CRITERIO_AND o CRITERIO_OR.
	 * @param mixed $pvalorParametro Valor sobe el cual se compara el criterio.
	 */
	public function __construct($poperadorWhere) {
		parent::__construct(null, null);
		$this->operadorWhere = $poperadorWhere;
		$this->listaCriterios = new ListaCriteriosQuery();
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarWhere($excluirNulos = true) {
		// Ejecuto el "generarWhere" del subcriterio con el nuevo operador logico.
		if (count($this->listaCriterios->criterios) > 0) {
			$strCriterios = $this->listaCriterios->generarWhere($this->operadorWhere, $excluirNulos);
			return ($strCriterios != '') ? sprintf(" (%s) ", $strCriterios) : null;
		}
		else
			return null;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function obtenerTipoBindParam($excluirNulos = true) {
		// Genero el string con los tipos de cada criterio del subcriterio
		$parametrosTipo = '';

		foreach ($this->listaCriterios->criterios as $c) 
			$parametrosTipo .= $c->obtenerTipoBindParam($excluirNulos);

		return $parametrosTipo;
	}

}

?>