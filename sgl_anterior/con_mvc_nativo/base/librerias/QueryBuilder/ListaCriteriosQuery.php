<?php
require_once(PATH_KRAKEN_LIBRERIAS_QUERYBUILDER.'ConstantesQueryBuilder.php');

/**
 * Colección para gestionar los distintos criterios de selección utilizados en SelectQueryBuilder.
 */
class ListaCriteriosQuery {

	public $criterios;

	/**
	 * Constructor de clase.
	 */
	public function __construct() {
		$this->criterios = array();
	}

	/**
	 * Agrega un elemento de tipo CriterioQuerySimple a la colección. Es el equivalente a comparaciones directas.
	 * @param  string $ptipo           Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna        Nombre de la columna sobre la cual aplicar el criterio.
	 * @param  string $poperadorLogico Operador lógico de comparación. Pueden utilizarse las constantes IGUAL_A, DISTINTO_A, MAYOR_A, MAYOR_IGUAL_A, MENOR_A, MENOR_IGUAL_A.
	 * @param  mixed $pvalorParametro Valor del parametro con el cual comparar. DEBE SER una variable y esta fuese 'null', el criterio queda excluido de la query.
	 */
	public function agregarCriterioSimple($ptipo, $pcolumna, $poperadorLogico, $pvalorParametro) {
		$this->criterios[] = new CriterioQuerySimple($ptipo, $pcolumna, $poperadorLogico, $pvalorParametro);
	}

	/**
	 * Agrega un elemento de tipo CriterioQueryLike a la colección. Es el equivalente al operador LIKE de MySQL.
	 * @param  string $ptipo           Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna        Nombre de la columna sobre la cual aplicar el criterio.
	 * @param  string $pprefijoLike    Prefijo del operador LIKE del criterio. Por ejemplo '%'.
	 * @param  mixed $pvalorParametro Valor del parametro con el cual comparar. DEBE SER una variable y esta fuese 'null', el criterio queda excluido de la query.
	 * @param  string $psufijoLike     Sufijo del operador LIKE del criterio. Por ejemplo '%'.
	 */
	public function agregarCriterioLike($ptipo, $pcolumna, $pprefijoLike, $pvalorParametro, $psufijoLike) {
		$this->criterios[] = new CriterioQueryLike($ptipo, $pcolumna, $pprefijoLike, $pvalorParametro, $psufijoLike);
	}

	/**
	 * Agrega un criterio de tipo CriterioQueryConstante a la colección.
	 * @param  string $pcriterioConstante Criterio de comparación arbitrario. Por ejemplo: 'activo = 1'
	 */
	public function agregarCriterioConstante($pcriterioConstante) {
		$this->criterios[] = new CriterioQueryConstante($pcriterioConstante);
	}

	/**
	 * Agrega un elemento de tipo CriterioQueryMultiple a la colección. Es el equivalente al operador IN de MySQL.
	 * @param  string $ptipo           Tipo de dato de la columna. Pueden utilizarse las constantes P_INT, P_TEXT, P_FLOAT, P_BLOB.
	 * @param  string $pcolumna        Nombre de la columna sobre la cual aplicar el criterio.
	 * @param  array $pconjuntoParametros Array con los elementos contra los cuales comparar.
	 */
	public function agregarCriterioMultiple($ptipo, $pcolumna, $pconjuntoParametros) {
		$this->criterios[] = new CriterioQueryMultiple($ptipo, $pcolumna, $pconjuntoParametros);
	}

	/**
	 * Agrega un elemento de tipo agregarSubCriterio a la colección. Se utiliza para intercalar "AND" y "OR" en los criterios de consulta.
	 * Si la lista de criterios no tiene elementos, no agrega nada.
	 * @param string $poperadorWhere Operador lógico con el cual unir los criterios del WHERE. Puede ser CRITERIO_AND o CRITERIO_OR.
	 * @param ListaCriteriosQuery $pListaCriterios  Intanciad de ListaCriteriosQuery la cual forma el subcriterio a aplicar.
	 */
	public function agregarSubCriterio($pOperadorWhere, ListaCriteriosQuery $pListaCriterios) {
		// Si la lista de criterios no tiene elementos, no agrega nada.
		if (count($pListaCriterios->criterios) > 0) {
			$subCriterio = new CriterioQuerySubCriterio($pOperadorWhere);
			$subCriterio->listaCriterios = $pListaCriterios;
			$this->criterios[] = $subCriterio;
		}
	}	

	/**
	 * Elimina todos los criterios especificados.
	 */
	public function limpiarCriterios() {
		$this->criterios = array();
	}

	/**
	* Determina si hay criterios en la lista.
	* @return bool TRUE si hay criterios, FALSE en caso contrario.
	*/
	public function hayCriterios($excluirNulos = true) {
		if (!$excluirNulos)
			$cant = count($this->criterios);
		else {
			$cant = 0;
			foreach ($this->criterios as $c) 
				if (!is_null($c->generarWhere())) {
					$cant++;
					break; // no necesito seguir buscando...
				}
		}
		return $cant > 0; 
	}

	/**
	 * Genera la cláusula WHERE en base a los criteros especificados.
	 * @param  string  $operadorLogico Operador lógico con el cual se unen los criterios.
	 * @param  boolean $excluirNulos   Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return string Cláusula WHERE.
	 */
	public function generarWhere($operadorLogico, $excluirNulos = true) {
		$strParams = array();

		// Genero todos los criterios variables
		foreach ($this->criterios as $p) {
			// Genero solo los criterios que no son null (depende de la forma de generar el where)
			$unCriterioWhere = $p->generarWhere($excluirNulos);
			if ($unCriterioWhere !== null)
				$strParams[] = $unCriterioWhere;
		}

		// Agrego el operador lógico				
		$salida = '';
		if (count($strParams) > 0) {
			$salida = implode(sprintf(' %s ', $operadorLogico), $strParams);
			$salida = sprintf(' (%s) ', $salida);
		}
		
		return $salida;
	}

	/**
	 * Genera el indicador de tipo de dato de todos los criterios para ser utilizado luego por el método bind_param de mysqli.
	 * @param  boolean $excluirNulos Si es true, los criterios que comparen contra variables null se excluyen de la generación. En caso de ser false, todos los criterios se incluyen en la generación.
	 * @return array
	 */
	public function generarBindParams($excluirNulos = true) {
		// Bind de criterios. Tipos: s = string, i = integer, d = double,  b = blob 
		$criteriosBind = array();

		// Genero el string con los tipos de criterios (1er parametro del bind_params)
		$parametrosTipo = '';
		foreach ($this->criterios as $c) 
			$parametrosTipo .= $c->obtenerTipoBindParam($excluirNulos);

		// Si no tengo tipos de parametro, significa que no hay nada para el bind_params
		// De ser asi, devuelvo un array vacio
		if ($parametrosTipo == '') {
			return array();
		
		// Si tengo parametros, entonces los proceso
		} else {
			// Al usar "call_user_func_array", los criterios tienen que ser pasados por referencia (con &)
			$criteriosBind[] = &$parametrosTipo;

			// Genero el resto de los criterios
			foreach ($this->criterios as $c) 
				// depende del tipo de criterio, es como obtengo la referencia al parametro
				//TODO: esto es horrible, pero no hay tiempo para mejorarlo ahora.
				if (($c instanceof CriterioQuerySimple) || ($c instanceof CriterioQueryLike)) {
					if ($excluirNulos) {
						if ($c->valorParametro !== null)
							$criteriosBind[] = &$c->valorParametro; // Al usar "call_user_func_array", los criterios tienen que ser pasados por referencia (con &)
					} else
						$criteriosBind[] = &$c->valorParametro; // Al usar "call_user_func_array", los criterios tienen que ser pasados por referencia (con &)
				} 
				else if ($c instanceof CriterioQueryMultiple) {
					if ($c->conjuntoParametros !== null) 
						foreach ($c->conjuntoParametros as &$p) {
							$criteriosBind[] = &$p;
						}
				}
				else if ($c instanceof CriterioQuerySubCriterio) {
					// genero los parametros del subcriterio
					$subBind = $c->listaCriterios->generarBindParams($excluirNulos);
					if (count($subBind) > 0) {
						unset($subBind[0]); // elimino el primer elemento, porque es el "paramtrosTipo" del subquery
						$criteriosBind = array_merge($criteriosBind, $subBind); // agrego el resto de los bind del subcriterio
					}
				}

			// Devuelvo resultados
			return $criteriosBind;
		}
	}

}