<?php
/**
 * Capa de acceso a datos (persistencia) específica de la gestion de Configuracion.
 */
class DBConfiguracion extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $t_parametros = 'admin_parametros'; //!< // Identificador de tabla 'admin_parametros'

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * DBParametros: Obtiene un array de filas correspondientes a la clase Parametro en base a diferentes criterios de selección.
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerParametros(
		// Parametros
		$pparametro = null,
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null,	
		// Control de consulta
		array $pOrdenColumnas = null, 
		$pLimiteCantidad = null, 
		$pLimiteOffset = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			T.`parametro`,
			T.`val_int`,
			T.`val_string`,
			T.`val_datetime`,
			T.`val_text`,
			T.`val_double`
		FROM 
			`{$this->t_parametros}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.parametro', '%', $pparametro, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.val_int', IGUAL_A, $pval_int);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.val_string', '%', $pval_string, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.val_datetime', IGUAL_A, $pval_datetime);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.val_text', IGUAL_A, $pval_text);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.val_double', IGUAL_A, $pval_double);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBParametros: Obtiene la cantidad de filas correspondientes de la clase Parametro en base a una consulta con diferentes criterios de selección.
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 * @return int
	 */
	public function obtenerParametrosCantidad(
		// Parametros
		$pparametro = null,
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_parametros}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.parametro', '%', $pparametro, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.val_int', IGUAL_A, $pval_int);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.val_string', '%', $pval_string, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.val_datetime', IGUAL_A, $pval_datetime);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.val_text', IGUAL_A, $pval_text);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.val_double', IGUAL_A, $pval_double);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBParametros: Guarda una instancia de la clase Parametro en la base de datos.
	 * @param  string (PK) parametro
	 * @param  integer val_int
	 * @param  string val_string
	 * @param  string val_datetime
	 * @param  string val_text
	 * @param  float val_double
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarParametro(
		// Parametros
		$pparametro = null,
		$pval_int = null,
		$pval_string = null,
		$pval_datetime = null,
		$pval_text = null,
		$pval_double = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_parametros;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'parametro', $pparametro);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'val_int', $pval_int);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'val_string', $pval_string);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'val_datetime', $pval_datetime);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'val_text', $pval_text);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'val_double', $pval_double);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;		
	}

	/**
	 * DBParametros: Elimina una instancia de la clase Parametro en la base de datos.
	 * @param  integer $pparametro 
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarParametros($pparametro = null)
	{
		// Por cuestiones de seguridad, no dejo que el parametro sea null
		if (is_null($pparametro))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarParametros: debe especificar parametro.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_parametros;

		// Mapeo de campos
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'parametro', IGUAL_A, $pparametro);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}
}
?>