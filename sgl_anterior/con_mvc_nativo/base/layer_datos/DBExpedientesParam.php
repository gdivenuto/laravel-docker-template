<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de tablas paramétricas de Expedientes.
 */
class DBExpedientesParam extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************

	protected $t_expe_codcategoria = 'expe_codcategoria'; //!< // Identificador de tabla 'expe_codcategoria'
	protected $t_expe_codestados = 'expe_codestados'; //!< // Identificador de tabla 'expe_codestados'
	protected $t_expe_codtemas = 'expe_codtemas'; //!< // Identificador de tabla 'expe_codtemas'
	protected $t_expe_lugares = 'expe_lugares'; //!< // Identificador de tabla 'expe_lugares'
	protected $t_expe_codproyectos = 'expe_codproyectos'; //!< // Identificador de tabla 'expe_codproyectos'
	protected $t_admin_comisiones_internas = 'admin_comisiones_internas'; //!< // Identificador de tabla 'admin_comisiones_internas'
	protected $t_admin_usuarios = 'admin_usuarios'; //!< // Identificador de tabla 'admin_usuarios'
	protected $t_pers_personal = 'pers_personal'; //!< // Identificador de tabla 'pers_personal'

	protected $const_mail_area_comisiones = SGL_MAIL_AREA_COMISIONES;

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
	// Codcategorias
	//
	// 07/01/2022 XXXX: se retira el campo "codigo_categoria"
	// ************************************************************************

	/**
	 * DBExpedientesParam: Obtiene un array de filas correspondientes a la clase Codcategoria en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerCodcategorias(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null,
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
			T.`id_codcategoria`,
			T.`descripcion_categoria`,
			T.`vigencia_desde_categoria`,
			T.`vigencia_hasta_categoria`,
			T.`habilitado_categoria`,
			T.`id_usuario`
		FROM
			`{$this->t_expe_codcategoria}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.descripcion_categoria', '%', $pdescripcion_categoria, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_categoria', IGUAL_A, $pvigencia_desde_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_categoria', IGUAL_A, $pvigencia_hasta_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_categoria', IGUAL_A, $phabilitado_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesParam: Obtiene la cantidad de filas correspondientes de la clase Codcategoria en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodcategoriasCantidad(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_codcategoria}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_categoria', IGUAL_A, $pdescripcion_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_categoria', IGUAL_A, $pvigencia_desde_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_categoria', IGUAL_A, $pvigencia_hasta_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_categoria', IGUAL_A, $phabilitado_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesParam: Guarda una instancia de la clase Codcategoria en la base de datos.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarCodcategoria(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codcategoria;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_codcategoria', $pid_codcategoria);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'descripcion_categoria', $pdescripcion_categoria);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_desde_categoria', $pvigencia_desde_categoria);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_hasta_categoria', $pvigencia_hasta_categoria);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_categoria', $phabilitado_categoria);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientesParam: Elimina un conjunto de Codcategorias en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:49
	 * @param  integer (PK) id_codcategoria
	 * @param  string descripcion_categoria
	 * @param  string vigencia_desde_categoria
	 * @param  string vigencia_hasta_categoria
	 * @param  string habilitado_categoria
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarCodcategorias(
		// Parametros
		$pid_codcategoria = null,
		$pdescripcion_categoria = null,
		$pvigencia_desde_categoria = null,
		$pvigencia_hasta_categoria = null,
		$phabilitado_categoria = null,
		$pid_usuario = null)
	{
		// is_null($pcodigo_categoria) &&
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_codcategoria) && is_null($pdescripcion_categoria) && is_null($pvigencia_desde_categoria) && is_null($pvigencia_hasta_categoria) && is_null($phabilitado_categoria) && is_null($pid_usuario))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarCodcategorias: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codcategoria;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codcategoria', IGUAL_A, $pid_codcategoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'descripcion_categoria', IGUAL_A, $pdescripcion_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_desde_categoria', IGUAL_A, $pvigencia_desde_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_hasta_categoria', IGUAL_A, $pvigencia_hasta_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_categoria', IGUAL_A, $phabilitado_categoria);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Codestado
	//
	//  07/01/2022 XXXX, se retiró el parámetro $pcodigo_estado
	// ************************************************************************

	/**
	 * DBExpedientesParam: Obtiene un array de filas correspondientes a la clase Codestado en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerCodestados(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null,
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
			T.`id_codestado`,
			T.`nombre_estado`,
			T.`vigencia_desde_codestado`,
			T.`vigencia_hasta_codestado`,
			T.`observaciones_codestado`,
			T.`habilitado_codestado`,
			T.`id_usuario`,
			T.`tratamiento_comision`
		FROM
			`{$this->t_expe_codestados}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.nombre_estado', '%', $pnombre_estado, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_codestado', IGUAL_A, $pvigencia_desde_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_codestado', IGUAL_A, $pvigencia_hasta_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_codestado', IGUAL_A, $pobservaciones_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_codestado', IGUAL_A, $phabilitado_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesParam: Obtiene la cantidad de filas correspondientes de la clase Codestado en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @return int
	 */
	public function obtenerCodestadosCantidad(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_codestados}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.nombre_estado', IGUAL_A, $pnombre_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_codestado', IGUAL_A, $pvigencia_desde_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_codestado', IGUAL_A, $pvigencia_hasta_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_codestado', IGUAL_A, $pobservaciones_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_codestado', IGUAL_A, $phabilitado_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesParam: Guarda una instancia de la clase Codestado en la base de datos.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarCodestado(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codestados;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_codestado', $pid_codestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'nombre_estado', $pnombre_estado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_desde_codestado', $pvigencia_desde_codestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_hasta_codestado', $pvigencia_hasta_codestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_codestado', $pobservaciones_codestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_codestado', $phabilitado_codestado);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'tratamiento_comision', $this->boolToInt($ptratamiento_comision));

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientesParam: Elimina un conjunto de Codestados en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codestado
	 * @param  string nombre_estado
	 * @param  string vigencia_desde_codestado
	 * @param  string vigencia_hasta_codestado
	 * @param  string observaciones_codestado
	 * @param  string habilitado_codestado
	 * @param  integer id_usuario
	 * @param  bool tratamiento_comision
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarCodestados(
		// Parametros
		$pid_codestado = null,
		$pnombre_estado = null,
		$pvigencia_desde_codestado = null,
		$pvigencia_hasta_codestado = null,
		$pobservaciones_codestado = null,
		$phabilitado_codestado = null,
		$pid_usuario = null,
		$ptratamiento_comision = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_codestado) && is_null($pnombre_estado) && is_null($pvigencia_desde_codestado) && is_null($pvigencia_hasta_codestado) && is_null($pobservaciones_codestado) && is_null($phabilitado_codestado) && is_null($pid_usuario) && is_null($ptratamiento_comision))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarCodestados: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codestados;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codestado', IGUAL_A, $pid_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'nombre_estado', IGUAL_A, $pnombre_estado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_desde_codestado', IGUAL_A, $pvigencia_desde_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_hasta_codestado', IGUAL_A, $pvigencia_hasta_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_codestado', IGUAL_A, $pobservaciones_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_codestado', IGUAL_A, $phabilitado_codestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'tratamiento_comision', IGUAL_A, $this->boolToInt($ptratamiento_comision));

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Codtema
	//
	// 07/01/2022 XXXX, se retiró el parámetro $pcodigo_tema
	// ************************************************************************

	/**
	 * DBExpedientesParam: Obtiene un array de filas correspondientes a la clase Codtema en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerCodtemas(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null,
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
			T.`id_codtema`,
			T.`descripcion_tema`,
			T.`vigencia_desde_tema`,
			T.`vigencia_hasta_tema`,
			T.`habilitado_tema`,
			T.`id_usuario`
		FROM
			`{$this->t_expe_codtemas}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.descripcion_tema', '%', $pdescripcion_tema, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_tema', IGUAL_A, $pvigencia_desde_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_tema', IGUAL_A, $pvigencia_hasta_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_tema', IGUAL_A, $phabilitado_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesParam: Obtiene la cantidad de filas correspondientes de la clase Codtema en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodtemasCantidad(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_codtemas}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_tema', IGUAL_A, $pdescripcion_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_tema', IGUAL_A, $pvigencia_desde_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_tema', IGUAL_A, $pvigencia_hasta_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_tema', IGUAL_A, $phabilitado_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesParam: Guarda una instancia de la clase Codtema en la base de datos.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarCodtema(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codtemas;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_codtema', $pid_codtema);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'descripcion_tema', $pdescripcion_tema);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_desde_tema', $pvigencia_desde_tema);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_hasta_tema', $pvigencia_hasta_tema);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_tema', $phabilitado_tema);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientesParam: Elimina un conjunto de Codtemas en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codtema
	 * @param  string descripcion_tema
	 * @param  string vigencia_desde_tema
	 * @param  string vigencia_hasta_tema
	 * @param  string habilitado_tema
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarCodtemas(
		// Parametros
		$pid_codtema = null,
		$pdescripcion_tema = null,
		$pvigencia_desde_tema = null,
		$pvigencia_hasta_tema = null,
		$phabilitado_tema = null,
		$pid_usuario = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_codtema) && is_null($pdescripcion_tema) && is_null($pvigencia_desde_tema) && is_null($pvigencia_hasta_tema) && is_null($phabilitado_tema) && is_null($pid_usuario))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarCodtemas: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codtemas;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codtema', IGUAL_A, $pid_codtema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'descripcion_tema', IGUAL_A, $pdescripcion_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_desde_tema', IGUAL_A, $pvigencia_desde_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_hasta_tema', IGUAL_A, $pvigencia_hasta_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_tema', IGUAL_A, $phabilitado_tema);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Lugares
	// ************************************************************************

	/**
	 * DBExpedientesParam: Obtiene un array de filas correspondientes a la clase Lugar en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerLugares(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null,
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
			T.`tipo_grp`,
			T.`codigo_grp`,
			T.`descripcion_grp`,
			T.`abreviatura_grp`,
			T.`bloque_tipo`,
			T.`bloque_codigo`,
			T.`observaciones_grp`,
			T.`vigente_Desde_grp`,
			T.`vigente_Hasta_grp`,
			T.`habilitado_grp`,
			T.`id_usuario`
		FROM
			`{$this->t_expe_lugares}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		// Configuro los criterios de WHERE
		if ( !is_null($ptipo_grp) && is_array($ptipo_grp)) {
			$builder->criteriosWhere->agregarCriterioMultiple(P_TEXT, 'T.tipo_grp', $ptipo_grp);
		}
		else
			$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_grp', IGUAL_A, $ptipo_grp);

		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_grp', IGUAL_A, $pcodigo_grp);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'T.descripcion_grp', '%', $pdescripcion_grp, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.abreviatura_grp', IGUAL_A, $pabreviatura_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.bloque_tipo', IGUAL_A, $pbloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.bloque_codigo', IGUAL_A, $pbloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_grp', IGUAL_A, $pobservaciones_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigente_Desde_grp', IGUAL_A, $pvigente_Desde_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigente_Hasta_grp', IGUAL_A, $pvigente_Hasta_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_grp', IGUAL_A, $phabilitado_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesParam: Obtiene la cantidad de filas correspondientes de la clase Lugar en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerLugaresCantidad(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_lugares}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo_grp', IGUAL_A, $ptipo_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.codigo_grp', IGUAL_A, $pcodigo_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_grp', IGUAL_A, $pdescripcion_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.abreviatura_grp', IGUAL_A, $pabreviatura_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.bloque_tipo', IGUAL_A, $pbloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.bloque_codigo', IGUAL_A, $pbloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones_grp', IGUAL_A, $pobservaciones_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigente_Desde_grp', IGUAL_A, $pvigente_Desde_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigente_Hasta_grp', IGUAL_A, $pvigente_Hasta_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_grp', IGUAL_A, $phabilitado_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesParam: Guarda una instancia de la clase Lugar en la base de datos.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarLugar(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_lugares;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo_grp', $ptipo_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'codigo_grp', $pcodigo_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'descripcion_grp', $pdescripcion_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'abreviatura_grp', $pabreviatura_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'bloque_tipo', $pbloque_tipo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'bloque_codigo', $pbloque_codigo);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones_grp', $pobservaciones_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigente_Desde_grp', $pvigente_Desde_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigente_Hasta_grp', $pvigente_Hasta_grp);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_grp', $phabilitado_grp);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientesParam: Elimina un conjunto de Lugares en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:11
	 * @param  string (PK) tipo_grp
	 * @param  string (PK) codigo_grp
	 * @param  string descripcion_grp
	 * @param  string abreviatura_grp
	 * @param  string bloque_tipo
	 * @param  string bloque_codigo
	 * @param  string observaciones_grp
	 * @param  string vigente_Desde_grp
	 * @param  string vigente_Hasta_grp
	 * @param  string habilitado_grp
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarLugares(
		// Parametros
		$ptipo_grp = null,
		$pcodigo_grp = null,
		$pdescripcion_grp = null,
		$pabreviatura_grp = null,
		$pbloque_tipo = null,
		$pbloque_codigo = null,
		$pobservaciones_grp = null,
		$pvigente_Desde_grp = null,
		$pvigente_Hasta_grp = null,
		$phabilitado_grp = null,
		$pid_usuario = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($ptipo_grp) && is_null($pcodigo_grp) && is_null($pdescripcion_grp) && is_null($pabreviatura_grp) && is_null($pbloque_tipo) && is_null($pbloque_codigo) && is_null($pobservaciones_grp) && is_null($pvigente_Desde_grp) && is_null($pvigente_Hasta_grp) && is_null($phabilitado_grp) && is_null($pid_usuario))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarLugares: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_lugares;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo_grp', IGUAL_A, $ptipo_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'codigo_grp', IGUAL_A, $pcodigo_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'descripcion_grp', IGUAL_A, $pdescripcion_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'abreviatura_grp', IGUAL_A, $pabreviatura_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'bloque_tipo', IGUAL_A, $pbloque_tipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'bloque_codigo', IGUAL_A, $pbloque_codigo);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones_grp', IGUAL_A, $pobservaciones_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigente_Desde_grp', IGUAL_A, $pvigente_Desde_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigente_Hasta_grp', IGUAL_A, $pvigente_Hasta_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_grp', IGUAL_A, $phabilitado_grp);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Codproyecto
	//
	// 07/01/2022 XXXX, se retiró el parámetro "pcodigo_proyecto"
	// ************************************************************************

	/**
	 * DBExpedientesParam: Obtiene un array de filas correspondientes a la clase Codproyecto en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:50:57
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerCodproyectos(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null,
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
			CodP.`id_codproyecto`,
			CodP.`descripcion_proyecto`,
			CodP.`vigencia_desde_codproy`,
			CodP.`vigencia_hasta_codproy`,
			CodP.`habilitado_codproy`,
			CodP.`id_usuario`
		FROM
			`{$this->t_expe_codproyectos}` as CodP
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'CodP.id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioLike(P_TEXT, 'CodP.descripcion_proyecto', '%', $pdescripcion_proyecto, '%');
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'CodP.vigencia_desde_codproy', IGUAL_A, $pvigencia_desde_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'CodP.vigencia_hasta_codproy', IGUAL_A, $pvigencia_hasta_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'CodP.habilitado_codproy', IGUAL_A, $phabilitado_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'CodP.id_usuario', IGUAL_A, $pid_usuario);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;

		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBExpedientesParam: Obtiene la cantidad de filas correspondientes de la clase Codproyecto en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @return int
	 */
	public function obtenerCodproyectosCantidad(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			count(*) as cantidad
		FROM
			`{$this->t_expe_codproyectos}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.descripcion_proyecto', IGUAL_A, $pdescripcion_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_desde_codproy', IGUAL_A, $pvigencia_desde_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.vigencia_hasta_codproy', IGUAL_A, $pvigencia_hasta_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.habilitado_codproy', IGUAL_A, $phabilitado_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBExpedientesParam: Guarda una instancia de la clase Codproyecto en la base de datos.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarCodproyecto(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codproyectos;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeoAutoincremental(P_INT, 'id_codproyecto', $pid_codproyecto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'descripcion_proyecto', $pdescripcion_proyecto);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_desde_codproy', $pvigencia_desde_codproy);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'vigencia_hasta_codproy', $pvigencia_hasta_codproy);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'habilitado_codproy', $phabilitado_codproy);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario', $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * DBExpedientesParam: Elimina un conjunto de Codproyectos en base a diferentes criterios de selección.
	 * GenerateClass 0.97.6 beta @ 2016-10-13 10:51:05
	 * @param  integer (PK) id_codproyecto
	 * @param  string descripcion_proyecto
	 * @param  string vigencia_desde_codproy
	 * @param  string vigencia_hasta_codproy
	 * @param  string habilitado_codproy
	 * @param  integer id_usuario
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarCodproyectos(
		// Parametros
		$pid_codproyecto = null,
		$pdescripcion_proyecto = null,
		$pvigencia_desde_codproy = null,
		$pvigencia_hasta_codproy = null,
		$phabilitado_codproy = null,
		$pid_usuario = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($pid_codproyecto) && is_null($pdescripcion_proyecto) && is_null($pvigencia_desde_codproy) && is_null($pvigencia_hasta_codproy) && is_null($phabilitado_codproy) && is_null($pid_usuario))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarCodproyectos: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_codproyectos;

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_codproyecto', IGUAL_A, $pid_codproyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'descripcion_proyecto', IGUAL_A, $pdescripcion_proyecto);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_desde_codproy', IGUAL_A, $pvigencia_desde_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'vigencia_hasta_codproy', IGUAL_A, $pvigencia_hasta_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'habilitado_codproy', IGUAL_A, $phabilitado_codproy);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario', IGUAL_A, $pid_usuario);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	// ************************************************************************
	// Datos de comisiones
	//
	// ************************************************************************
	/**
	 * DBExpedientesParam: Obtiene el mail de notificación para una determinada comision.
	 * En caso de no haber relatora asociada o no haber un mail válido, se toma el mail
	 * del área de comisiones (constante de configuracion).
	 * @param  [type] $pci_codigo [description]
	 * @return [type]             [description]
	 */
	public function obtenerMailComision($pci_codigo = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT
			IFNULL(U.`u_mail`, IFNULL(P.`p_mail`, '{$this->const_mail_area_comisiones}')) as ci_mail
		FROM `{$this->t_admin_comisiones_internas}` AS CI
		LEFT JOIN `{$this->t_admin_usuarios}` AS U ON
			(U.`u_legajo` = CI.`ci_relator`)
		LEFT JOIN `{$this->t_pers_personal}` AS P ON
			(P.`p_legajo` = CI.`ci_relator`)
SQL;
// HereDoc End -------------------------------------------------------------------------------------

		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'CI.ci_codigo', IGUAL_A, $pci_codigo);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return (count($resultado) > 0)
			? $resultado[0]['ci_mail']
			: null;
	}

}
?>
