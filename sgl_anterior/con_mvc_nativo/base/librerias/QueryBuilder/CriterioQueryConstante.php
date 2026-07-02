<?php
/**
 * Criterio de tipo constante.
 */
class CriterioQueryConstante extends CriterioQuery {

	public $criterioConstante;

	/**
	 * Constructor de clase
	 * @param string $pcriterioConstante    Criterio arbitrario a aplicar
	 */
	public function __construct($pcriterioConstante) {
		parent::__construct(null, null);
		$this->criterioConstante = $pcriterioConstante;
	}

	/**
	 * Genera el criterio para la cláusula WHERE.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function generarWhere($excluirNulos = true) {
		$resultado = $this->criterioConstante;

		// Genero solo los criterios que no son null (depende de la forma de generar el where)
		if ($excluirNulos) 
			if ($this->criterioConstante === null)
				$resultado = null;

		return $resultado;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string
	 */
	public function obtenerTipoBindParam($excluirNulos = true) {
		// el CriterioQueryConstante no devuelve ningun tipo para el bind_params
		return '';
	}

}

?>