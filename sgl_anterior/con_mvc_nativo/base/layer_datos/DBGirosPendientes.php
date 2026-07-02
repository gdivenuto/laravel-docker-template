<?php

/**
 * Capa de acceso a datos (persistencia) específica de la gestion de giros pendientes a comisiones.
 */
class DBGirosPendientes extends DBBaseClass {
	// ************************************************************************
	// Definición de Atributos ************************************************
	// ************************************************************************
	protected $t_expe_giros_pendientes = 'expe_giros_pendientes'; //!< // Identificador de tabla 'expe_giros_pendientes'
	protected $t_admin_usuarios = 'admin_usuarios'; //!< Identificador de tabla 'admin_usuarios'
	
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
	 * DBGirosPendientes: Obtiene un array de filas correspondientes a la clase GiroPendiente en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones	 
	 * @param  array|null $pOrdenColumnas Array de strings donde cada elemento define un criterio de ordenamiento para el resultado obtenido. Puede determinarse el sentido del ordenamiento, por ejemplo 'edad asc' o 'identificador desc'.
	 * @param  integer $pLimiteCantidad Determina la cantidad maxima de resultados permitidos para una consulta (el resultado se trunca a esta cantidad si fuera mayor). Es equivalente al modificador LIMIT de MySQL, por ejemplo 'LIMIT 10'.
	 * @param  integer $pLimiteOffset Corrimiento de resultados devueltos, utilizado en conjunto con $pLimiteCantidad. Equivalente a LIMIT de MySQL, por ejemplo 'LIMIT 1, 10'.
	 * @return array
	 */
	public function obtenerGirosPendientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null,	
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
			T.`anio`,
			T.`tipo`,
			T.`numero`,
			T.`cuerpo`,
			T.`alcance`,
			T.`id_pendiente`,
			T.`giros_pendientes`,
			T.`estado`,
			T.`fecha_hora_entrada`,
			T.`fecha_hora_salida`,
			T.`id_usuario_firmante`,
			T.`id_usuario_solicitante`,
			T.`observaciones`,
			USR_F.`codigo_usuario` as ro_codigo_usuario_firmante,
			USR_F.`nombre_usuario` as ro_nombre_usuario_firmante,
			USR_F.`u_mail` as ro_mail_usuario_firmante,
			USR_S.`codigo_usuario` as ro_codigo_usuario_solicitante,
			USR_S.`nombre_usuario` as ro_nombre_usuario_solicitante,
			USR_S.`u_mail` as ro_mail_usuario_solicitante

		FROM 
			`{$this->t_expe_giros_pendientes}` as T

		LEFT JOIN `{$this->t_admin_usuarios}` as USR_F ON
			(T.`id_usuario_firmante` = USR_F.`id_usuario`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR_S ON
			(T.`id_usuario_solicitante` = USR_S.`id_usuario`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_pendiente', IGUAL_A, $pid_pendiente);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.giros_pendientes', IGUAL_A, $pgiros_pendientes);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_firmante', IGUAL_A, $pid_usuario_firmante);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * DBGirosPendientes: Obtiene la cantidad de filas correspondientes de la clase GiroPendiente en base a una consulta con diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones	 
	 * @return int
	 */
	public function obtenerGirosPendientesCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_expe_giros_pendientes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_pendiente', IGUAL_A, $pid_pendiente);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.giros_pendientes', IGUAL_A, $pgiros_pendientes);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_firmante', IGUAL_A, $pid_usuario_firmante);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

	/**
	 * DBGirosPendientes: Guarda una instancia de la clase GiroPendiente en la base de datos.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones	 
	 * @return mixed Si la entidad posee algun atributo autoincremental devuelve el valor generado, o cero en caso contrario.
	 */
	public function guardarGiroPendiente(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		// Instancia del SaveQueryBuilder
		$builder = new SaveQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_giros_pendientes;

		// Mapeo de campos
		$builder->mapeoCampos->agregarMapeo(P_INT, 'anio', $panio);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'tipo', $ptipo);
		$builder->mapeoCampos->agregarMapeo(P_FLOAT, 'numero', $pnumero);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'cuerpo', $pcuerpo);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'alcance', $palcance);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_pendiente', $pid_pendiente);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'giros_pendientes', $pgiros_pendientes);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'estado', $pestado);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_entrada', $pfecha_hora_entrada);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'fecha_hora_salida', $pfecha_hora_salida);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario_firmante', $pid_usuario_firmante);
		$builder->mapeoCampos->agregarMapeo(P_INT, 'id_usuario_solicitante', $pid_usuario_solicitante);
		$builder->mapeoCampos->agregarMapeo(P_TEXT, 'observaciones', $pobservaciones);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;		
	}

	/**
	 * DBGirosPendientes: Elimina un conjunto de GirosPendientes en base a diferentes criterios de selección.
	 * GenerateClass 0.97.7 beta @ 2022-10-31 10:21:36
	 * @param  integer (PK) anio
	 * @param  string (PK) tipo
	 * @param  float (PK) numero
	 * @param  integer (PK) cuerpo
	 * @param  integer (PK) alcance
	 * @param  integer (PK) id_pendiente
	 * @param  string giros_pendientes
	 * @param  string estado
	 * @param  string fecha_hora_entrada
	 * @param  string fecha_hora_salida
	 * @param  integer id_usuario_firmante
	 * @param  integer id_usuario_solicitante
	 * @param  string observaciones	 
	 * @return integer Cantidad de filas afectadas.
	 */
	public function eliminarGirosPendientes(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		// Por cuestiones de seguridad, no dejo que todos los parametros sean null.
		if (is_null($panio) && is_null($ptipo) && is_null($pnumero) && is_null($pcuerpo) && is_null($palcance) && is_null($pid_pendiente) && is_null($pgiros_pendientes) && is_null($pestado) && is_null($pfecha_hora_entrada) && is_null($pfecha_hora_salida) && is_null($pid_usuario_firmante) && is_null($pid_usuario_solicitante) && is_null($pobservaciones))
			throw new RuntimeException(sprintf("Fall&oacute; la ejecución de %s.eliminarGirosPendientes: para evitar el borrado accidental de datos, no es posible eliminar utilizando todos los parámetros como nulos.", get_class($this)));

		// Instancia del DeleteQueryBuilder
		$builder = new DeleteQueryBuilder();

		// Tabla
		$builder->nombreTabla = $this->t_expe_giros_pendientes;
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_pendiente', IGUAL_A, $pid_pendiente);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'giros_pendientes', IGUAL_A, $pgiros_pendientes);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario_firmante', IGUAL_A, $pid_usuario_firmante);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'observaciones', IGUAL_A, $pobservaciones);

		//Logger::get()->Log("builder_eliminarGirosPendientes_".date("Ymd_His"), $builder->getQuery);

		// Ejecuto y devuelvo resultados
		$resultado = $this->ejecutarNoConsulta($builder);

		return $resultado;
	}

	/**
	 * Obtiene el siguiente número de id pendiente para un lote de giros de un expediente.
	 * @param  [type] $panio    [description]
	 * @param  [type] $ptipo    [description]
	 * @param  [type] $pnumero  [description]
	 * @param  [type] $pcuerpo  [description]
	 * @param  [type] $palcance [description]
	 * @return [type]           [description]
	 */
	public function obtenerGiroPendienteIdSiguiente(
		// Parametros
		$panio,
		$ptipo,
		$pnumero,
		$pcuerpo,
		$palcance) 
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			IFNULL(MAX(id_pendiente)+1, 1) as max_id_pendiente
		FROM 
			`{$this->t_expe_giros_pendientes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['max_id_pendiente'];
	}

	/**
	 * Ejecuta la misma lógica que obtenerGirosPendientes, con la diferencia que
	 * al filtrar por usuario solicitante o firmante, incluye en la respuesta 
	 * aquellos giros que pertenecen al usuario solicitante o al usuario firmante 
	 * (utilizando el operador OR). Con esto se determina quienes tienen permiso
	 * a operar sobre dichos giros pendientes (el solicitante o el firmante).
	 * @param  [type]     $panio                   [description]
	 * @param  [type]     $ptipo                   [description]
	 * @param  [type]     $pnumero                 [description]
	 * @param  [type]     $pcuerpo                 [description]
	 * @param  [type]     $palcance                [description]
	 * @param  [type]     $pid_pendiente           [description]
	 * @param  [type]     $pgiros_pendientes       [description]
	 * @param  [type]     $pestado                 [description]
	 * @param  [type]     $pfecha_hora_entrada     [description]
	 * @param  [type]     $pfecha_hora_salida      [description]
	 * @param  [type]     $pid_usuario_firmante    [description]
	 * @param  [type]     $pid_usuario_solicitante [description]
	 * @param  [type]     $pobservaciones          [description]
	 * @param  array|null $pOrdenColumnas          [description]
	 * @param  [type]     $pLimiteCantidad         [description]
	 * @param  [type]     $pLimiteOffset           [description]
	 * @return [type]                              [description]
	 */
	public function obtenerGirosPendientesUsuario(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null,	
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
			T.`anio`,
			T.`tipo`,
			T.`numero`,
			T.`cuerpo`,
			T.`alcance`,
			T.`id_pendiente`,
			T.`giros_pendientes`,
			T.`estado`,
			T.`fecha_hora_entrada`,
			T.`fecha_hora_salida`,
			T.`id_usuario_firmante`,
			T.`id_usuario_solicitante`,
			T.`observaciones`,
			USR_F.`codigo_usuario` as ro_codigo_usuario_firmante,
			USR_F.`nombre_usuario` as ro_nombre_usuario_firmante,
			USR_F.`u_mail` as ro_mail_usuario_firmante,
			USR_S.`codigo_usuario` as ro_codigo_usuario_solicitante,
			USR_S.`nombre_usuario` as ro_nombre_usuario_solicitante,
			USR_S.`u_mail` as ro_mail_usuario_solicitante

		FROM 
			`{$this->t_expe_giros_pendientes}` as T

		LEFT JOIN `{$this->t_admin_usuarios}` as USR_F ON
			(T.`id_usuario_firmante` = USR_F.`id_usuario`)
		LEFT JOIN `{$this->t_admin_usuarios}` as USR_S ON
			(T.`id_usuario_solicitante` = USR_S.`id_usuario`)

SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_pendiente', IGUAL_A, $pid_pendiente);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.giros_pendientes', IGUAL_A, $pgiros_pendientes);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// ----- Que el usuario firmante O el solicitiante cumplan con la condición
		$subCrit = new ListaCriteriosQuery();

		// El usuario firmante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario_firmante) && is_array($pid_usuario_firmante)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario_firmante', $pid_usuario_firmante);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario_firmante', IGUAL_A, $pid_usuario_firmante);

		// El usuario solicitante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario_solicitante) && is_array($pid_usuario_solicitante)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario_solicitante', $pid_usuario_solicitante);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);

		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);
		// ----------------------------

		// Agrego el ORDER BY
		$builder->criteriosOrderBy = $pOrdenColumnas;

		// Agrego el LIMIT
		$builder->limiteCantidad = $pLimiteCantidad;
		$builder->limiteOffset = $pLimiteOffset;
		
		// Ejecuto la consulta
		return $this->obtenerResultadosConsulta($builder);
	}

	/**
	 * Ejecuta la misma lógica que obtenerGirosPendientesCantidad, con la diferencia que
	 * al filtrar por usuario solicitante o firmante, incluye en la respuesta 
	 * aquellos giros que pertenecen al usuario solicitante o al usuario firmante 
	 * (utilizando el operador OR). Con esto se determina quienes tienen permiso
	 * a operar sobre dichos giros pendientes (el solicitante o el firmante).
	 * @param  [type] $panio                   [description]
	 * @param  [type] $ptipo                   [description]
	 * @param  [type] $pnumero                 [description]
	 * @param  [type] $pcuerpo                 [description]
	 * @param  [type] $palcance                [description]
	 * @param  [type] $pid_pendiente           [description]
	 * @param  [type] $pgiros_pendientes       [description]
	 * @param  [type] $pestado                 [description]
	 * @param  [type] $pfecha_hora_entrada     [description]
	 * @param  [type] $pfecha_hora_salida      [description]
	 * @param  [type] $pid_usuario_firmante    [description]
	 * @param  [type] $pid_usuario_solicitante [description]
	 * @param  [type] $pobservaciones          [description]
	 * @return [type]                          [description]
	 */
	public function obtenerGirosPendientesUsuarioCantidad(
		// Parametros
		$panio = null,
		$ptipo = null,
		$pnumero = null,
		$pcuerpo = null,
		$palcance = null,
		$pid_pendiente = null,
		$pgiros_pendientes = null,
		$pestado = null,
		$pfecha_hora_entrada = null,
		$pfecha_hora_salida = null,
		$pid_usuario_firmante = null,
		$pid_usuario_solicitante = null,
		$pobservaciones = null)
	{
		// Instancia del SelectQueryBuilder
		$builder = new SelectQueryBuilder();

// HereDoc Start -----------------------------------------------------------------------------------
		$builder->cabecera = <<<SQL
		SELECT 
			count(*) as cantidad
		FROM 
			`{$this->t_expe_giros_pendientes}` as T
SQL;
// HereDoc End -------------------------------------------------------------------------------------
		
		// Configuro los criterios de WHERE
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.anio', IGUAL_A, $panio);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.tipo', IGUAL_A, $ptipo);
		$builder->criteriosWhere->agregarCriterioSimple(P_FLOAT, 'T.numero', IGUAL_A, $pnumero);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.cuerpo', IGUAL_A, $pcuerpo);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.alcance', IGUAL_A, $palcance);
		$builder->criteriosWhere->agregarCriterioSimple(P_INT, 'T.id_pendiente', IGUAL_A, $pid_pendiente);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.giros_pendientes', IGUAL_A, $pgiros_pendientes);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.estado', IGUAL_A, $pestado);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_entrada', IGUAL_A, $pfecha_hora_entrada);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.fecha_hora_salida', IGUAL_A, $pfecha_hora_salida);
		$builder->criteriosWhere->agregarCriterioSimple(P_TEXT, 'T.observaciones', IGUAL_A, $pobservaciones);

		// ----- Que el usuario firmante O el solicitiante cumplan con la condición
		$subCrit = new ListaCriteriosQuery();

		// El usuario firmante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario_firmante) && is_array($pid_usuario_firmante)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario_firmante', $pid_usuario_firmante);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario_firmante', IGUAL_A, $pid_usuario_firmante);

		// El usuario solicitante puede ser un array de id's de usuarios
		if ( !is_null($pid_usuario_solicitante) && is_array($pid_usuario_solicitante)) 
			$subCrit->agregarCriterioMultiple(P_INT, 'T.id_usuario_solicitante', $pid_usuario_solicitante);
		else
			$subCrit->agregarCriterioSimple(P_INT, 'T.id_usuario_solicitante', IGUAL_A, $pid_usuario_solicitante);

		$builder->criteriosWhere->agregarSubCriterio(CRITERIO_OR, $subCrit);
		// ----------------------------

		// Ejecuto la consulta
		$resultado = $this->obtenerResultadosConsulta($builder);

		return $resultado[0]['cantidad'];
	}

}
?>