<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de tablas de 
 * Transacciones de Actuaciones.
 */
class DBTransacActuaciones extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $t_expe_transac_actuaciones = 'expe_transac_actuaciones'; //!< // Identificador de tabla 'expe_transac_actuaciones'

	// ************************************************************************
	// Definición de Métodos **************************************************
	// ************************************************************************
	/**
	 * Constructor de clase
	 */
	public function __construct() {
		parent::__construct();
	}

	// ************************************************************************
	// Transacciones de Actuaciones
	// ************************************************************************

	/**
	 * DBActuaciones: Obtiene un array de filas correspondientes a la clase TransacActuacion en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data	 
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerTransacActuaciones(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null,	
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
			T.`id_transaccion`,
			T.`id_paso`,
			T.`tipo_actuacion`,
			T.`fecha_hora`,
			T.`id_usuario`,
			T.`data`
		FROM 
			`{$this->t_expe_transac_actuaciones}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_transaccion', IGUAL_A, $pid_transaccion);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_paso', IGUAL_A, $pid_paso);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.data', IGUAL_A, $pdata);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBActuaciones: Obtiene la cantidad de filas correspondientes de la clase TransacActuacion en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data	 
	 * @return int
	 */
	public function obtenerTransacActuacionesCantidad(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_expe_transac_actuaciones}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_transaccion', IGUAL_A, $pid_transaccion);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_paso', IGUAL_A, $pid_paso);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.data', IGUAL_A, $pdata);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBActuaciones: Guarda una instancia de la clase TransacActuacion en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data	 
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarTransacActuacion(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_transac_actuaciones;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_transaccion', $pid_transaccion);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_paso', $pid_paso);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo_actuacion', $ptipo_actuacion);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora', $pfecha_hora);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'data', $pdata);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;		
	}

	/**
	 * DBActuaciones: Elimina un conjunto de TransacActuaciones en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-08-10 13:50:42
	 * @param  integer (PK) id_transaccion
	 * @param  integer (PK) id_paso
	 * @param  string tipo_actuacion
	 * @param  string fecha_hora
	 * @param  integer id_usuario
	 * @param  string data	 
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarTransacActuaciones(
		// Parametros
		$pid_transaccion = null,
		$pid_paso = null,
		$ptipo_actuacion = null,
		$pfecha_hora = null,
		$pid_usuario = null,
		$pdata = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_transaccion) && is_null($pid_paso) && is_null($ptipo_actuacion) && is_null($pfecha_hora) && is_null($pid_usuario) && is_null($pdata))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarTransacActuaciones: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_transac_actuaciones;
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_transaccion', IGUAL_A, $pid_transaccion);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_paso', IGUAL_A, $pid_paso);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo_actuacion', IGUAL_A, $ptipo_actuacion);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora', IGUAL_A, $pfecha_hora);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'data', IGUAL_A, $pdata);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	public function obtenerTransacActuacionesNuevoIdTransaccion() 
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			IFNULL(MAX(id_transaccion)+1, 1) as max_id_transaccion
		FROM 
			`{$this->t_expe_transac_actuaciones}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['max_id_transaccion'];
	}
}
?>