<?php
/**
 * Clase que contiene el mapeo de un campo.
 */
class MapeoCampo {

	public $tipo;				//!< Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	public $columna;			//!< Nombre de la columna sobre la cual aplicar el criterio.
	public $valor;				//!< Valor con el cual se desea mapear la columna.
	public $esAutoincremental;	//!< Indicador boolean para determinar si el campo es autoincremental o no.

	/**
	 * Constructor de clase.
	 * @param  string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna Nombre de la columna la cual se desea mapear.
	 * @param  mixed $pvalor   Valor del parametro con el cual se desea mapear la columna.
	 * @param boolean $pesAutoincremental Indicador boolean para determinar si el campo es autoincremental o no.
	 */
	public function __construct($ptipo, $pcolumna, $pvalor, $pesAutoincremental = false) {
		$this->tipo = $ptipo;
		$this->columna = $pcolumna;
		$this->valor = $pvalor;
		$this->esAutoincremental = $pesAutoincremental;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli. Aplica a
	 * los parametros utilizados en la instrucción INSERT.
	 */
	public function obtenerTipoBindParamInsert() {
		return $this->tipo;
	}

	/**
	 * Genera el indicador de tipo de dato para ser utilizado luego por el método bind_param de mysqli. Aplica a
	 * los parametros utilizados en la instrucción UPDATE.
	 */
	public function obtenerTipoBindParamUpdate() {
		if ($this->esAutoincremental)
			return '';
		else
			return $this->tipo;
	}
}

?>