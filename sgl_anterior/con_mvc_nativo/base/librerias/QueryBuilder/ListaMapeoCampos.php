<?php
require_once(PATH_KRAKEN_LIBRERIAS_QUERYBUILDER.'ConstantesQueryBuilder.php');

/**
 * Colección para gestionar los mapeos de campos a variables en SaveQueryBuilder.
 *
 * @author XXXX
 */
class ListaMapeoCampos {

	public $mapeos;

	/**
	 * Constructor de clase.
	 */
	public function __construct() {
		$this->mapeos = array();
	}

	/**
	 * Agrega un elemento de tipo MapeoCampo a la lista de mapeos.
	 * @param  string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna Nombre de la columna la cual se desea mapear.
	 * @param  mixed $pvalor   Valor del parametro con el cual se desea mapear la columna.
	 */
	public function agregarMapeo($ptipo, $pcolumna, $pvalor) {
		$this->mapeos[] = new MapeoCampo($ptipo, $pcolumna, $pvalor, false);
	}

	/**
	 * Agrega un elemento de tipo MapeoCampo a la lista de mapeos. Se utiliza cuando se desea mapear una columna que es de tipo autoincremetal.
	 * @param  string $ptipo    Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna Nombre de la columna la cual se desea mapear.
	 * @param  mixed $pvalor   Valor del parametro con el cual se desea mapear la columna.
	 */
	public function agregarMapeoAutoincremental($ptipo, $pcolumna, $pvalor) {
		$this->mapeos[] = new MapeoCampo($ptipo, $pcolumna, $pvalor, true);
	}

	/**
	 * Genera la porcion del codigo correspondiente al mapeo de la instrucción INSERT
	 * @return string
	 */
	public function generarMapeoInsert() {
		$strColumnas = array();
		$strValores = array();

		// Genero el listado de campos y de "values"
		foreach ($this->mapeos as $m) {
			$strColumnas[] = BuilderHelper::agregarBackquote($m->columna);
			$strValores[] = '?';
		}

		// Armo el cuerpo del insert
		$salida = '';
		if (count($strColumnas) > 0) {
			$salida = implode(', ', $strColumnas);
			$salida = sprintf('(%s) VALUES (%s)',
				implode(', ', $strColumnas),
				implode(', ', $strValores));
		}

		return $salida;
	}

	/**
	 * Genera la porcion del codigo correspondiente al mapeo de la instrucción UPDATE
	 * @return string
	 */
	public function generarMapeoUpdate() {
		$strMapeos = array();

		// Genero todos los criterios para el update
		foreach ($this->mapeos as $m) {
			if ($m->esAutoincremental)
				$strMapeos[] = sprintf('%s = last_insert_id(%s)',
					BuilderHelper::agregarBackquote($m->columna),
					BuilderHelper::agregarBackquote($m->columna));
			else
				$strMapeos[] = sprintf('%s = ?', BuilderHelper::agregarBackquote($m->columna));
		}

		// Armo el cuerpo del update
		$salida = '';
		if (count($strMapeos) > 0) {
			$salida = implode(', ', $strMapeos);
		}

		return $salida;
	}

	/**
	 * Genera el indicador de tipo de dato de todos los mapeos para ser utilizado luego por el método bind_param de mysqli.
	 * @return string
	 */
	public function generarBindParams() {
		// Bind de criterios. Tipos: s = string, i = integer, d = double,  b = blob
		$mapeosBind = array();

		// Genero el string con los mapeos para el insert (1er parametro del bind_params)
		$parametrosTipo = '';
		foreach ($this->mapeos as $m)
			$parametrosTipo .= $m->obtenerTipoBindParamInsert();

		// Ahora genero el string con los mapeos para el update, salteando los autoincrementales
		foreach ($this->mapeos as $m)
			$parametrosTipo .= $m->obtenerTipoBindParamUpdate();

		// Si no tengo tipos de parametro, significa que no hay nada para el bind_params
		// De ser asi, devuelvo un array vacio
		if ($parametrosTipo == '') {
			return array();

		// Si tengo parametros, entonces los proceso
		} else {
			// Al usar "call_user_func_array", los criterios tienen que ser pasados por referencia (con &)
			$mapeosBind[] = &$parametrosTipo;

			// Genero el resto de los criterios para el insert
			foreach ($this->mapeos as $m)
				$mapeosBind[] = &$m->valor;

			// Genero el resto de los criterios para el update
			foreach ($this->mapeos as $m)
				if (! $m->esAutoincremental) // salteo los autoincrementales
					$mapeosBind[] = &$m->valor;

			// Devuelvo resultados
			return $mapeosBind;
		}
	}

}
